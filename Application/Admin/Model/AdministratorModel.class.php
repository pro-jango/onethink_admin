<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class AdministratorModel extends Model {

    protected $_validate = array(
        array('username', '1,16', '用户名长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('username', '', '用户名被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用

        /* 验证密码 */
        array('password', '6,16', '密码长度不合法', self::EXISTS_VALIDATE, 'length'), //密码长度不合法

        /* 验证邮箱 */
        array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE), //邮箱格式不正确
        array('email', '1,32', '邮箱长度不合法', self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
        array('email', '', '邮箱被占用', self::EXISTS_VALIDATE, 'unique'), //邮箱被占用

        /* 验证手机号码 */
        array('mobile', '//', '手机格式不正确', self::EXISTS_VALIDATE), //手机格式不正确 TODO:
        array('mobile', '', '手机号被占用', self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );

    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($username,$password,$type = 1){
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['id'] = $username;
                break;
            default:
                return 0; //参数错误
        }

        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if(is_array($user) && $user['status']){
            /* 验证用户密码 */
            if(think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']){
                //记录行为
                action_log('user_login', 'administrator', $user['uid'], $user['uid']);

                /* 登录用户 */
                $this->autoLogin($user);

                return true;
            } else {
                $this->error = '密码错误！'; //应用级别禁用
                return false;
            }
        } else {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('admin_auth', null);
        session('admin_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        $map = array(
            'uid'             => $user['uid'],
        );
        /* 更新登录信息 */
        $data = array(
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->where($map)->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
        );

        session('admin_auth', $auth);
        session('admin_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->getField('nickname');
    }

    public function updateInfo($uid,$password,$data){
        if(empty($uid) || empty($password) || empty($data)){
            $return['status'] = false;
            $return['info'] = '参数错误！';
            return $return;
        }

        //更新前检查用户密码
        if(!$this->verifyUser($uid, $password)){
            $return['status'] = false;
            $return['info'] = '原密码验证错误！';
            return $return;
        }

        //更新用户信息
        $return['status'] = $this->where(array('uid'=>$uid))->save($data);
        if(!$return['status']){
            $return['status'] = '更新信息失败！';
        }
        return $return;
    }

    public function verifyUser($uid,$password){
        $_password = $this->getFieldById($uid, 'password');
        if(think_ucenter_md5($password, UC_AUTH_KEY) === $_password){
            return true;
        }else{
            return false;
        }
    }
}

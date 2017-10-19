<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Addons\SiteStat;
use Common\Controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */
class SiteStatAddon extends Addon{

    public $info = array(
        'name'=>'SiteStat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'status'=>1,
        'author'=>'thinkphp',
        'version'=>'0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        if($config['display']){
            $today = strtotime(date('Y-m-d'));
            $info['userToday']		=	M('ucenter_member')->where(array('reg_time'=>array('egt',$today)))->count();
            $info['userTotal']      =   M('ucenter_member')->count();

            $info['payToday']      =   M('weixin_pay')->where(array('status'=>1,'addtime'=>array('egt',$today)))->count();
            $info['payTotal']      =   M('weixin_pay')->where(array('status'=>1))->count();

            $info['orderToday']      =   M('orderlist')->where(array('addtime'=>array('egt',$today)))->count();
            $info['orderTotal']      =   M('orderlist')->count();


            $map['status'] = array('in',array(0));
            $info['orderConfirm']      =   M('orderlist')->where($map)->count();
            $map['status'] = array('in',array(10));
            $info['orderWaiting']      =   M('orderlist')->where($map)->count();
            $this->assign('info',$info);
            $this->display('info');
        }
    }
}
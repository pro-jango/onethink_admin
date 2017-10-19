<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Think;
//http://sms.duanxinwang.cc/
//帐号：西钥  密码：944338

class SmsVerify {
    /**
     * 验证验证码是否正确
     * @access public
     * @param string $mobile 手机号码
     * @param string $verify 验证码标识     
     * @return bool 用户验证码是否正确
     */
    public function check($mobile, $verify = '') {
        
        $model = M('mobilecode');
        $now = date('Y-m-d H:i:s');
        $list = $model->where("mobile='$mobile' AND outtime>'$now'")->order('id desc')->limit(1)->select();
        if(empty($list)){
            return false;
        }

        if($list[0]['verify'] == $verify){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 输出验证码并把验证码的值保存的session中
     * 验证码保存到session的格式为： array('verify_code' => '验证码值', 'verify_time' => '验证码创建时间');
     * @access public     
     * @param string $id 要生成验证码的标识   
     * @return void
     */
    public function sendVerify($mobile = '') {

        $model = M('mobilecode');
        $addtime = date('Y-m-d');
        $count = $model->where("mobile='$mobile' AND addtime>'$addtime'")->count();
        if($count >= 3){
            $info['rtn'] = -1;
            $info['message'] = "您的手机号码今天发送次数超过限制，请明天再试！";
            return $info;
        }

        $idata['mobile'] = $mobile;
        $idata['verify'] = rand(100000,999999);
        $idata['outtime'] = date('Y-m-d H:i:s',strtotime("+10 minutes"));
        $idata['addtime'] = date('Y-m-d H:i:s');

        $insertId = $model->data($idata)->add();
        if($insertId){
            $flag = 0; 
            $params='';//要post的数据 

            //以下信息自己填以下
            $mobile='';//手机号
            $argv = array( 
                'name'=>'西钥',     //必填参数。用户账号
                'pwd'=>'CFDC41EB390C8BB139627CFF3239',     //必填参数。（web平台：基本资料中的接口密码）
                'content'=>'您的短信验证码为：'.$idata['verify'].'，有效期10分钟，请勿将验证码提供给他人。',   //必填参数。发送内容（1-500 个汉字）UTF-8编码
                'mobile'=>$idata['mobile'],   //必填参数。手机号码。多个以英文逗号隔开
                'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
                'sign'=>'红石榴金融',    //必填参数。用户签名。
                'type'=>'pt',  //必填参数。固定值 pt
                'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
            ); 
            //print_r($argv);exit;
            //构造要post的字符串 
            //echo $argv['content'];
            foreach ($argv as $key=>$value) { 
                if ($flag!=0) { 
                    $params .= "&"; 
                    $flag = 1; 
                } 
                $params.= $key."="; $params.= urlencode($value);// urlencode($value); 
                $flag = 1; 
            } 
            $url = "http://web.cr6868.com/asmx/smsservice.aspx?".$params; //提交的url地址
            $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态
            \Think\Log::write("_send_sms[$url]=[$con]");
            if($con == '0'){
                $info['rtn'] = 0;
                $info['message'] = "发送成功";
                return $info;
            }else{
                $info['rtn'] = -3;
                $info['message'] = "系统繁忙，请稍后再试！";
                return $info;
            }
        }else{
            $info['rtn'] = -2;
            $info['message'] = "系统繁忙，请稍后再试！";
            return $info;
        }
    }

    /** 
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数) 
     *      
     *      高中的数学公式咋都忘了涅，写出来
     *		正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     *
     */
    private function _writeCurve() {
        $px = $py = 0;
        
        // 曲线前部分
        $A = mt_rand(1, $this->imageH/2);                  // 振幅
        $b = mt_rand(-$this->imageH/4, $this->imageH/4);   // Y轴方向偏移量
        $f = mt_rand(-$this->imageH/4, $this->imageH/4);   // X轴方向偏移量
        $T = mt_rand($this->imageH, $this->imageW*2);  // 周期
        $w = (2* M_PI)/$T;
                        
        $px1 = 0;  // 曲线横坐标起始位置
        $px2 = mt_rand($this->imageW/2, $this->imageW * 0.8);  // 曲线横坐标结束位置

        for ($px=$px1; $px<=$px2; $px = $px + 1) {
            if ($w!=0) {
                $py = $A * sin($w*$px + $f)+ $b + $this->imageH/2;  // y = Asin(ωx+φ) + b
                $i = (int) ($this->fontSize/5);
                while ($i > 0) {	
                    imagesetpixel($this->_image, $px + $i , $py + $i, $this->_color);  // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多				
                    $i--;
                }
            }
        }
        
        // 曲线后部分
        $A = mt_rand(1, $this->imageH/2);                  // 振幅		
        $f = mt_rand(-$this->imageH/4, $this->imageH/4);   // X轴方向偏移量
        $T = mt_rand($this->imageH, $this->imageW*2);  // 周期
        $w = (2* M_PI)/$T;		
        $b = $py - $A * sin($w*$px + $f) - $this->imageH/2;
        $px1 = $px2;
        $px2 = $this->imageW;

        for ($px=$px1; $px<=$px2; $px=$px+ 1) {
            if ($w!=0) {
                $py = $A * sin($w*$px + $f)+ $b + $this->imageH/2;  // y = Asin(ωx+φ) + b
                $i = (int) ($this->fontSize/5);
                while ($i > 0) {			
                    imagesetpixel($this->_image, $px + $i, $py + $i, $this->_color);	
                    $i--;
                }
            }
        }
    }

    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    private function _writeNoise() {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for($i = 0; $i < 10; $i++){
            //杂点颜色
            $noiseColor = imagecolorallocate($this->_image, mt_rand(150,225), mt_rand(150,225), mt_rand(150,225));
            for($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->_image, 5, mt_rand(-10, $this->imageW),  mt_rand(-10, $this->imageH), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }

    /**
     * 绘制背景图片
     * 注：如果验证码输出图片比较大，将占用比较多的系统资源
     */
    private function _background() {
        $path = dirname(__FILE__).'/Verify/bgs/';
        $dir = dir($path);

        $bgs = array();		
        while (false !== ($file = $dir->read())) {
            if($file[0] != '.' && substr($file, -4) == '.jpg') {
                $bgs[] = $path . $file;
            }
        }
        $dir->close();

        $gb = $bgs[array_rand($bgs)];

        list($width, $height) = @getimagesize($gb);
        // Resample
        $bgImage = @imagecreatefromjpeg($gb);
        @imagecopyresampled($this->_image, $bgImage, 0, 0, 0, 0, $this->imageW, $this->imageH, $width, $height);
        @imagedestroy($bgImage);
    }

    /* 加密验证码 */
    private function authcode($str){
        $key = substr(md5($this->seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }

}

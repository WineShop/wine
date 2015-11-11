<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 烟消云散 <1010422715@qq.com> <http://www.yershop.com>
// +----------------------------------------------------------------------

namespace Home\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {

    /* 用户中心首页 */
    public function index(){
        echo '<meta charset="utf-8"/>';
        echo '正在发送邮件..';
        $mail      ='920111264@qq.com';//获取会员邮箱
        $title     ="欢迎注册".C('SITENAME');
        $token     =sha1(C('DATA_AUTH_KEY'));
        $name      = $_SERVER['SERVER_NAME'];
        $url       = $_SERVER['SERVER_NAME'].U("wine/active/".$token);

        $this->assign('url',$url);
        $this->assign('mail',$mail);
        $this->assign('time',date('Y-m-d H:i:s',time()));
        $content = $this->fetch('User:index');
//            $content   ="您在".C('SITENAME')."注册了账号，<a href=\"".$url."\" target='_blank'>".$url.'</a>请点击激活'.$mail;

        $res = SendMail($mail,$title,$content);

        if($res)
        {
            S($token,$mail,3600*24*3);  //有效期3天
            echo '恭喜您，注册成功!';
        }else{
            echo '邮件发送失败，注册失败！';
        }
    }

    /* 注册页面 */
    public function showRegister(){
        if(!C("USER_ALLOW_REGISTER")){
            $this->error("注册已关闭");
        }
        $this->meta_title = '会员注册';
        $this->display();
    }

    /* 操作注册 */
    public function register($password = "", $repassword = "", $email = "", $verify = ""){

        /* 检测验证码 */
        if(!check_verify($verify)){
            $this->ajaxError('验证码输入错误！');
        }

        if(empty($password) || empty($repassword) || empty($email))
        {
            $this->ajaxError('对不起，不能为空');
        }
        /* 检测密码 */
        if($password != $repassword){
            $this->ajaxError('密码和重复密码不一致！');
        }

        /* 调用注册接口注册用户 */
        $User = new UserApi;
        //返回ucentermember数据表用户主键id
        $uid = $User->register($password, $email);
        if(0 < $uid){ //注册成功
            //TODO: 发送验证邮件
            $mail      =$_POST['email'];//获取会员邮箱
            $title     ="欢迎注册".C('SITENAME');
            $token     =sha1(C('DATA_AUTH_KEY'));
            //U("account/confirm_email",array('token'=>$token)  'wine/active/:token\w'
            $url       = $_SERVER['SERVER_NAME'].U("wine/active/".$token);

            $this->assign('url',$url);
            $this->assign('mail',$mail);
            $this->assign('time',date('Y-m-d H:i:s',time()));
            $content = $this->fetch('User:register');
//            $content   ="您在".C('SITENAME')."注册了账号，<a href=\"".$url."\" target='_blank'>".$url.'</a>请点击激活'.$mail;

            $res = SendMail($mail,$title,$content);
            if($res)
            {
                S($token,array('email'=>$mail,'uid'=>$uid),3600*24*3);  //有效期3天
                $this->ajaxSuccess("<h4>恭喜您，注册成功!</h4>请尽快到您的邮件中进行激活");
            }else{
                $User->deleteUserById($uid);
                $this->ajaxError('注册失败，邮件没有成功发送！');
            }

        } else { //注册失败，显示错误信息
            $this->ajaxError($this->showRegError($uid));
        }
    }

    //注册成功界面
    public function registerSuccess()
    {
        $this->display();
    }

    /* 登录页面 */
    public function login($username = "", $password = "", $verify = "",$is_forver=''){
        if(IS_POST){ //登录验证
            /* 检测验证码 */
            if(!check_verify($verify)){
                $this->ajaxError('验证码输入错误！');
                die();
            }

            /* 调用UC登录接口登录 */
            $user     = new UserApi;
            $userInfo = $user->login($username, $password,I('post.type'));
            if($userInfo['status'] == 'ok')
            {
                $user -> setUserCache($userInfo['msg'],$is_forver);        //设置用户缓存
                $this -> ajaxSuccess('登陆成功！');
            }else{
                $this->ajaxError($userInfo['msg']);
            }
        } else {
            $this->meta_title = '会员登录';
            $this->display();
        }
    }




    /* 退出登录 */
    public function logout(){
        if(is_login()){
            $user = new UserApi();
            $user->logout();
            $this->ajaxSuccess("退出成功！");
        } else {
            $this->redirect("Index/index");
        }
    }

    public function favor(){
        if(IS_AJAX ){
            if (!($uid = is_login())) {
                $this->ajaxError('您还没有登陆，请先登陆！');
            }
            $id         = $_POST["id"];
            $data["id"] = $id;
            $data["uid"]= $uid;
            $fav        = M("favortable");
            $exsit      = $fav->where("goodid='$id' and uid='$uid'")->getField("id");
            if(isset($exsit)){
                $this->ajaxError('您之前已成功收藏过！');
            }else{
                $fav->goodid = $id;
                $fav->uid    = $uid;
                $fav->create_time = time();
                $fav->add();
                $this->ajaxSuccess('该物品已成功收藏！');
            }
        }else{
            $this->error('对不起，请求有误！');
        }

    }
    /* 验证码，用于登录和注册 */
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->useCurve = false;
        $Verify->imageW   = 140;
        $Verify->imageH   = 40;
        $Verify->entry();
    }



    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = "用户名长度必须在16个字符以内！"; break;
            case -2:  $error = "用户名被禁止注册！"; break;
            case -3:  $error = "用户名被占用！"; break;
            case -4:  $error = "密码长度必须在6-30个字符之间！"; break;
            case -5:  $error = "邮箱格式不正确！"; break;
            case -6:  $error = "邮箱长度必须在1-32个字符之间！"; break;
            case -7:  $error = "邮箱被禁止注册！"; break;
            case -8:  $error = "邮箱被占用！"; break;
            case -9:  $error = "手机格式不正确！"; break;
            case -10: $error = "手机被禁止注册！"; break;
            case -11: $error = "手机号被占用！"; break;
            default:  $error = "未知错误";
        }
        return $error;
    }
    public function cart(){
        $cart=$_SESSION["cart"];
        if($cart){
            foreach($cart as $k=>$val){
                $id=$val["id"];
                $table->goodid=$id;
                $member=D("member");
                $uid=$member->uid();
                $table->uid=$uid;
                $table->partnerid=get_partnerid($uid);
                $num=M("shopcart")->where("goodid='$id'")->getField("num");
                if($num){
                    $table->num=$val["num"]+$num;
                    $table->save();
                }
                else{
                    $table->num=$val["num"];
                    $table->add();
                }
            }
            return $uid;
        }
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
        if (IS_POST) {
            if (!($uid = is_login())) {
                $this->ajaxError( "您还没有登陆，请先登陆");
            }
            //获取参数
            $password   =   I("post.old");
            $repassword = I("post.repassword");
            $data["password"] = I("post.password");
            empty($password) && $this->ajaxError("请输入原密码");
            empty($data["password"]) && $this->ajaxError("请输入新密码");
            empty($repassword) && $this->ajaxError("请输入确认密码");

            if($data["password"] !== $repassword){
                $this->ajaxError("您输入的新密码与确认密码不一致");
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                $Api ->logout();
                $this->ajaxSuccess("修改密码成功！请重新登录");
            }else{
                $this->ajaxError($res["info"]);
            }
        }else{
            if (!($uid = is_login()) ) {
                $this->error( "您还没有登陆，请先登陆",U('/'),2);
            }
            $this->meta_title = '修改密码';
            $this->display();
        }
    }

    /**
     * 验证优惠券是否可用
     */
    public function checkcode(){
        if (!($uid = is_login()) ) {
            $this->error( "您还没有登陆，请先登陆",U("/"),2);
        }
        /***接受代码统计 */
        $code    = $_POST["couponid"];
        $fcoupon = M("fcoupon");
        $id = $fcoupon->where("code='$code' ")->getfield("id");
        /***获取优惠券id,优惠券存在 */
        if(isset($id)){
            $coupon = M("usercoupon");
            /***用户优惠券存在 */
            if($coupon->where("uid='$uid'and couponid='$id' and status='1'")->select()){
                $msg   = "该优惠券可以使用";
                $this->ajaxSuccess($msg);
            }else{
                $msg    = "该优惠券已使用或未领取";
                $this->ajaxError($msg);
            }
        } else{
            /***获取优惠券id,优惠券不存在 */

            $msg      = "查询不到该优惠券";
            $this->ajaxError($msg);
        }

    }
    /*****领优惠券
     ***************/
    public  function getcoupon() {
        if (!($uid = is_login()) ) {
            $this->ajaxError( "您还没有登陆，请先登陆");
        }
        $id=$_POST["couponid"];
        if(empty($id))
            $this->ajaxError('对不起，参数有误！');

        $coupon=M("usercoupon");
        if($coupon->where("uid='$uid'and couponid='$id'")->select() )
        {
            $msg = "您之前已领取过";
            $this->ajaxError($msg);
        }else{
            $data["uid"] = $uid;
            $data["couponid"] = $id;
            $data["time"] = NOW_TIME;
            $data["status"] = "1";
            $data["info"] = "未使用";
            $coupon->add($data);
            $msg  = "恭喜您，已成功领取";

            $this->ajaxSuccess($msg);

        }

    }

    public  function cut() {
        if (!($uid = is_login()) ) {
            $this->error( "您还没有登陆，请先登陆",U("/"),2);
        }
        $id=$_POST["couponid"];
        $coupon=M("usercoupon");
        if($coupon->where("uid='$uid' and couponid='$id'")->select() )
        {
            $data["msg"] = "已领取过";
            $data["status"] = "0";
            $this->ajaxreturn($data);
        }else{
            $data["uid"] = $uid;
            $data["couponid"] = $id;
            $data["time"] = NOW_TIME;
            $data["status"] = "1";
            $data["info"] = "未使用";
            $coupon->add($data);
            $data["msg"] = "已成功领取，请刷新查看";

            $this->ajaxreturn($data);

        }
        $uid=I("get.id");
        $cut=M("member")->where("uid='$uid'")->select();
        $this->assign('cut',$cut);
        $images = new \Think\Image();
        if ($_POST['pic']){
            //$src=C('DOMAIN').$_POST["pic"];
            $src=$_POST['pic'];
            $images->open($src);
            $name= time().$src;
            $x=$_POST["x"];
            $y=$_POST["y"];	$w=$_POST["w"];
            $h=$_POST["h"];
            $s=$images->crop(400, 400,100,30)->save('./'.$name);
            echo $s;
        }
        $this->meta_title = '修改图像';
        $this->display();
    }


}

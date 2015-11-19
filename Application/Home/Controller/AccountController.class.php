<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 1010422715@qq.com  All rights reserved.
// +----------------------------------------------------------------------
// |  Author: kevin.liu <kevin.liu@yunzhihui.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
use User\Api\UserApi;
/*****个人中心***************/
class AccountController extends HomeController {


    public  function security() {
        $uid = $this->login();
        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        $this->assign('uid', $uid);

        /*最后一次登录时间*/
        $userCache = D('Member')->getUserCache();
        $this->assign('last_login_time', $userCache['last_login_time']);

        $this->meta_title = '安全中心';
        $verification = M("verification");
        $condition['uid'] = $uid;
        $verInfo = $verification->where($condition)->field('id,email,mobile')->find();
        $num1 = $num2 = $num3 = 0;
        if(empty($verInfo)){
           if(!empty($verInfo['email']))  $num1 = 1;
           if(!empty($verInfo['mobile'])) $num2 = 1;
            $verid  = $verInfo['id'];
        }
        $this->assign('emailid',$verid);

        //支付密码设置判断
        $str  = D("Member")->where("uid='$uid'")->getField('paykey');
        $code = encrypt($str,'D',''); //解密
        $this->assign('code', $code);

        if($code){$num3=1;}

        $num = $num1+$num2+$num3;
        $this->assign('num', $num);
        $this->display();

    }

    public  function checkemail() {
        $uid = $this->login();
        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        $this->assign('uid', $uid);

        $user = D('Member')->getUserCache();
        $this->assign('email', $user['email']);
        $this->meta_title = '验证邮箱';

        $this->display();

    }
    public  function paykey() {

        $uid = $this->login();

        $user = D('Member')->getUserCache();
        $this->assign('email', $user['email']);
        $this->meta_title = '支付密码设置';

        $code = D("member")->where("uid='$uid'")->getField('paykey');
        $this->assign('code', $code);
        $this->display();

    }

    public  function savepaykey() {
        if(IS_POST){
            $uid = is_login();
            if(!$uid)
                $this->ajaxError('对不起，请先登录！');

            if(I('post.paykey') != I('post.repaykey'))
                $this->ajaxError('对不起，两次密码不一致');

            $data            = array();
            $paykey          = encrypt(I('post.paykey'),'E');
            $data['paykey']  = $paykey;
            //说明是修改支付密码
            if(I('post.is_add') != 1){
                if(I('post.code') != $paykey){
                    $this->ajaxError('对不起，输入的原来支付密码不对！');
                }
            }

            $member = D("member");
            $member->create();
            if($member->where("uid='$uid'")->save($data)){
                $this->ajaxSuccess("您已成功设置！") ;
            }else{
                $this->ajaxError("对不起，设置失败！")  ;
            }

        }else{
            $this->error("对不起，访问有误！")  ;
        }
    }

    public  function checkmobile() {
        $uid = $this->login();
        $this->assign('uid', $uid);

        $_SESSION['send_code'] = random(6,1);//生成随机加密码
        $this->meta_title = '验证手机';
        $this->display();

    }


    /** 发送短信验证 **/
    public  function send_sms() {
        if(IS_AJAX){
            //判断是否验证过
            $mobile    = $_POST['mobile'];
            $send_code = $_POST['send_code'];//获取提交随机加密码
            $content   = "您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。";
            $result    = sendsmscode($mobile,$content,$send_code);

            $this->ajaxReturn($result);

        }
    }

    /** 验证短信 **/
    public  function checksmscode() {

        if($_POST['mobile']!=$_SESSION['mobile'] or $_POST['mobile_code']!=$_SESSION['mobile_code'] or empty($_POST['mobile']) or empty($_POST['mobile_code'])){
            $data['msg']="手机验证码输入错误";
            $data['status']=0;
        }else{
            $_SESSION['mobile'] = '';
            $_SESSION['mobile_code'] = '';
            $data['msg']="验证成功";
            $data['status']=1;
            $verification=M("verification");
            $uid=D("member")->uid();
            $data['mobile']=$_POST['mobile'];
            $data['create_time']=NOW_TIME;
            $data['tag']=2;
            $data['uid']=$uid;
            $verification->create();
            $verification->add($data);
        }
        $this->ajaxReturn($data);
    }



    /** 发送邮箱验证 **/
    public  function send_email() {
        if(IS_AJAX){
            $uid=is_login();
            if(!$uid)
                $this->ajaxError('对不起，您还没有登陆');

            $user  = D('Member')->getUserCache();
            $mail  = $user['email'];

            $verification = M("verification");
            $ver_email    = $verification->where(array('uid'=>$uid,'email'=>$mail))->field('email')->find();
            if(!empty($ver_email))
                $this->ajaxError('您已验证过，无须重复验证！');

            $title = "邮箱验证";
            $token = Md5("{$uid}_{$mail}_check");
            $name  = $_SERVER['SERVER_NAME'];
            $url   = $_SERVER['SERVER_NAME'].U("wine/check/".$token);

            $this->assign('mail',$mail);
            $this->assign('time',date('Y-m-d H:i:s'));
            $this->assign('url',$url);
            $content=$this->fetch('User:checkmail');
            if(SendMail($mail,$title,$content)){
                $data['msg']      = '已成功发送验证信息到您的邮件！';
                $data['damain']   = $url;
                $data['uid']      = $uid;
                $data['content']  = $content;
                $data['account']  = $mail;
                $data['username'] = $user['username'];
                $data['create_time']= NOW_TIME;
                $email            = M("email");
                $data['sendname'] = "system";
                $data['status']   = 1;
                $email->create();
                $email->add($data);

                S($token,array('email'=>$mail,'uid'=>$uid),3600*24*3);  //有效期3天
                $this->ajaxSuccess($data);

            }else{
                $this->ajaxError('发送失败,无效的邮箱地址');
            }
        }else{
            $this->error('对不起，访问有误');
        }
    }
    /** 激活邮箱 **/
    public  function confirm_email() {
        $token = I("get.token");
        if(empty($token))
        {
            $title = '激活失败';
            $tip   = '激活链接地址参数有误！';
        }else{
            $userInfo = S($token);
            if(empty($userInfo))
            {
                $title = '激活失败';
                $tip   = '激活链接地址已经过期';
                $is_ok = 'no';
            }else{
                //验证用户
                $user_email = $userInfo['email'];
                $uid        = $userInfo['uid'];
                $mail       = get_email($uid);
                if($mail != $user_email)
                {
                    $title  = '激活失败';
                    $tip    = '激活链接地址参数有误！';
                    $is_ok  = 'no';
                }else{
                    $userModel = new UserApi;
                    $userModel->updateUserStatus($uid,1);
                    $title = '激活成功';
                    $tip   = '已激活成功';
                    $is_ok = 'yes';
                    //清空本次的缓存key
                    S($token,null);
                    $this->assign('email',$mail);
                }
            }
        }

        $this->assign('tip',$tip);
        $this->assign('is_ok',$is_ok);
        $this->assign('title',$title);
        $this->display('User/confirm_email');

    }

    public function check_email()
    {
        $token = I("get.token");
        if(empty($token))
        {
            $title = '验证失败';
            $tip   = '验证链接地址参数有误！';
            $is_ok = 'no';
        }else{
            $userInfo = S($token);
            if(empty($userInfo))
            {
                $title = '验证失败';
                $tip   = '验证链接地址已经过期';
                $is_ok = 'no';
            }else{
                //验证用户
                $user_email = $userInfo['email'];
                $uid        = $userInfo['uid'];
                $mail       = get_email($uid);
                if($mail != $user_email)
                {
                    $title  = '验证失败';
                    $tip    = '验证链接地址参数有误！';
                    $is_ok  = 'no';
                }else{
                    $verification  = M("verification");
                    $data['email'] = $mail;
                    $data['create_time']=NOW_TIME;
                    $data['status'] = 1;
                    $data['tag']    = 1;
                    $data['uid']    = $uid;
                    $verification->create();
                    $verification->add($data);

                    $title = '验证成功';
                    $tip   = '已验证成功';
                    $is_ok = 'yes';

                    //清空本次的缓存key
                    S($token,null);
                    $this->assign('email',$mail);
                }
            }
        }

        $this->assign('tip',$tip);
        $this->assign('is_ok',$is_ok);
        $this->assign('title',$title);
        $this->display('User/confirm_email');
    }

    public function history() {
        $uid = $this->login();
        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        $this->assign('uid', $uid);

        /**必须**/

        $User =M("history");
        $count= $User->where("uid='$uid'")->count();
        $this->assign('count',$count);// 赋值输出
        $Page= new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $list=$User->where("uid='$uid'")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '登录历史';
        $this->display();

    }

}

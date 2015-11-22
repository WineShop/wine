<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;
use Think\Controller;

/**
 * 后台首页控制器
 */
class PublicController extends Controller {

    public function __construct()
    {
        //防止QCC攻击
        checkCsf();
        parent::__construct();
    }
    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null){
        if(IS_POST){ //登录验证
            /* 检测验证码 */
            /*if(!check_verify($verify)){
                \Think\RestTool::instance()->error('验证码输入错误！');
            }*/
            $type   = I('post.type');
            if(empty($username) || empty($password))
            {
                \Think\RestTool::instance()->error('用户名和密码都不能为空！');
            }

            /* 调用UC登录接口登录 */
            $user     = new UserApi;
            $userInfo = $user->login($username, $password,$type,true);
            if($userInfo['status'] == 'ok')
            {
                $is_forver = true;
                $user -> setUserCache($userInfo['msg'],$is_forver);        //设置用户缓存
                \Think\RestTool::instance()->success(array(),1000,'登陆成功！');
            }else{
                \Think\RestTool::instance()->error($userInfo['msg']);
            }
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config	=	S('DB_CONFIG_DATA');
                if(!$config){
                    $config	=	D('Config')->lists();
                    S('DB_CONFIG_DATA',$config);
                }
                C($config); //添加配置

                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout()
    {
        if(is_login()){
            $user = new UserApi();
            $user->logout();
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect("Index/index");
        }
    }

    public function verify()
    {
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

}

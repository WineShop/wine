<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}


    protected function _initialize(){
        //防止QCC攻击
        checkCsf();
        /* 读取站点配置*/
        if(S(C('HOME_SITE_CONFIG'))){
            $config = S(C('HOME_SITE_CONFIG'));
            C($config); //添加配置
        }else{
            $config = api('Config/lists');
            S(C('HOME_SITE_CONFIG'),$config,3600*24*30);
            C($config); //添加配置
        }

       /* if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }*/
    }

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！');
	}

    /* 用于ajax返回错误信息 */
    protected function ajaxError($msg = NULL, $status = 900, $data = array())
    {
        \Think\RestTool::instance()->error($msg, $status, $data);
    }

    /* 用于ajax返回正确信息 */
    protected function ajaxSuccess($data=null,$status = 1000, $msg = null)
    {
        if(!is_array($data))
            $msg = $data;
        \Think\RestTool::instance()->success($data, $status, $msg);
    }

}

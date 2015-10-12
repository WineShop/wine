<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// |  Author: 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
$url= $_SERVER[HTTP_HOST]; //获取当前域名
 */
class IndexController extends HomeController {


    /**系统首页**/
    public function index(){
        /**首页统计代码实现**/
        $ip_tongji = C('IP_TONGJI');
        if(1 == $ip_tongji){
            $id     = "index";
            $record = IpLookup("",1,$id);
        }

        /*$user = M('category');
        $id   = $user->where('display=1 and pid=0')->getField('id',true);
        $this->assign('arrr',$id);*/

        /** 幻灯片调用* */
        if(S(C('HOME_SLIDE_BANNER'))){
            $slide = S(C('HOME_SLIDE_BANNER'));
        }else{
            $slide  =  get_slide();
            S(C('HOME_SLIDE_BANNER'),$slide,3600*24*30);
        }
        $this->assign('slide',$slide);

        /** 限时抢购调用* */
        /*$timelist=$this->timelist();
        $this->assign('timelist',$timelist);*/

        /** 最新上架调用**/
        /*$bytime=$this->bytime();
        $this->assign('bytime',$bytime);*/

        /** 热卖调用*/
        /*$totalsales=$this->totalsales();
        $this->assign('totalsales',$totalsales);
        $Carousel=$this->Carousel();
        $this->assign('carousel',$Carousel);*/

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);


        /** 底部分类调用**/
        if(S(C('HOME_FOOT_MENU')))
        {
            $menulist = S(C('HOME_FOOT_MENU'));
        }else{
            $menulist = R('Service/AllMenu');
            S(C('HOME_FOOT_MENU'),$menulist,3600*24*30);
        }
        $this->assign('footermenu',$menulist);


        /**   主体商品内容    **/
        if(S(C('HOME_SHOP_CENTER')))
        {
            $tree = S(C('HOME_SHOP_CENTER'));
        }else{
            $tree = $this->maketree() ;
            S(C('HOME_SHOP_CENTER'),$tree,3600*24*5);
        }
        $this->assign ( 'category', $tree);

        $this->meta_title = '首页';
        $this->display();
    }

    /**限时抢购**/
    public function timelist(){

        $time=M('document_product')->order('id desc')->where('mark="2"')->limit("6")->select();
        return $time;

    }
    /**幻灯片**/
    public function Carousel(){

        $Carousel=M('document')->where('position="4"')->select();
        return $Carousel;

    }
    /**热门搜索热词**/
    public function getHotsearch(){
        $arr = array();
        $str=M('config')->where('id="40"')->getField("value");
        $hotsearch=explode(",",$str);
        return $hotsearch;

    }
    /**最新上架**/
    public function bytime(){

        $bytime=M('document_product')->order('id desc')->limit("6")->select();
        return $bytime;

    }
    /**热卖商品**/
    public function totalsales(){

        $totalsales=M('document')->order('sale desc')->limit("6")->select();
        return $totalsales;

    }


    /**分类商品**/
    public function goodlist(){

        $str=M('brand')->where('status="1"')->order('ypid')->select();
        return $str;

    }


    /**循环遍历**/
    public function makeTree(){
        $category = D ( 'Category' )->getTree ();
        foreach ( $category as $k => $v ) {
            $cid=array();$arr=array();
            array_push($cid,$v['id']);
            array_push($arr,$v['id']);
            foreach ( $v ['_'] as $ks => $vs ) {
                array_push($cid,$vs['id']);
                foreach ( $vs ['_'] as $kgs => $vgs ) {
                    array_push($cid,$vgs['id']);
                }
            }
            $category [$k] ['doc'] = array ();
            $map['category_id']    = array("in",$cid);
            $map['status']         = 1;
            $category [$k] ['chi'] = array ();
            $condition['pid']      = array('in',$arr);
            $condition['ismenu']   = 1;
            $category [$k] ['chi'] = M('category')->where($condition)->field('id,name,title,pid,ismenu')->limit(18)->order("id desc")->select();
            $category [$k]['doc']  = M('Document')->where($map)->field('id,title,category_id,price,pid')->order("id desc")->limit(18)->select();
        }
        return $category;

    }

    /**
     * 获取首页的活动
     */
    public function ajaxActive()
    {
        $activity = M('document')->order('id desc')->where("category_id='70'")->field('id,title')->limit(8)->select();
        if(empty($activity))
        {
            $this->ajaxError('暂无记录');
        }else{
            $this->ajaxSuccess($activity);
        }

    }

    /**
     * 获取首页的公告
     */
    public function ajaxNotice()
    {
        $notice = M('document')->order('id desc')->where("category_id='56'")->field('id,title')->limit(8)->select();
        if(empty($notice))
        {
            $this->ajaxError('暂无记录');
        }else{
            $this->ajaxSuccess($notice);
        }
    }

    public function shopCenter()
    {
        /**   主体商品内容    **/
        if(S(C('HOME_SHOP_CENTER')))
        {
            $tree = S(C('HOME_SHOP_CENTER'));
        }else{
            $tree = $this->maketree() ;
            S(C('HOME_SHOP_CENTER'),$tree,3600*24*5);
        }
        $this->assign ( 'category', $tree);
        $this->display();
    }
}
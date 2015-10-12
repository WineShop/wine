<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 15-10-12
 * Time: 下午6:40
 */

namespace Home\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用菜单导航 和 购物车 信息
 */

class MenuWidget extends Controller{

    /* 显示指定分类的同级分类或子分类列表 */
    public function nav(){

        /** 首页导航先从缓存取 **/
        if(S(C('HOME_CATE_MENU'))){
            $catelist = S(C('HOME_CATE_MENU'));
        }else{
            $cate     = M('Category');
            $catelist = $this->menulist();
            S(C('HOME_CATE_MENU'),$catelist,3600*24*30);
        }
        $this->assign('categoryq', $catelist);

        /**购物车调用**/
        $cart=R("shopcart/usercart");
        $this->assign('usercart',$cart);
        if(!session('user_auth')){
            $usercart=$_SESSION['cart'];
            $this->assign('usercart',$usercart);
        }

        $this->display('Index/menu');
    }


    /**无限极分类菜单调用**/
    public function menulist(){
        $field = 'id,name,pid,title';
        $categoryq = D('Category')->field($field)->order('sort desc')->where('display="1"and ismenu="1" ')->select();
        $catelist = $this->unlimitedForLevel($categoryq);
        return $catelist;
    }

    public function unlimitedForLevel($cate,$name = 'child',$pid = 0){
        $arr = array();
        foreach ($cate as $key => $v) {
            //判断，如果$v['pid'] == $pid的则压入数组Child
            if ($v['pid'] == $pid) {
                //递归执行
                $v[$name] = self::unlimitedForLevel($cate,$name,$v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

}

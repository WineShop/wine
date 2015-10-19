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

class publicWidget extends Controller{

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

        $cart = $_SESSION['cart'];

        $total_num   = $this->getNum();
        $total_price = $this->getPrice();
        $this->assign('usercart',$cart);
        $this->assign('total_num',$total_num);
        $this->assign('total_price',$total_price);
        $this->display('Public/menu');
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


    /**
     * 查询购物车中商品的种类
     */
    public function getCnt() {
        $data = $_SESSION['cart'];
        return count($data);
    }

    /**
     * 查询购物车中商品的个数
     */
    public function getNum(){
        if ($this->getCnt() == 0) {
            //种数为0，个数也为0
            return 0;
        }
        $sum = 0;
        $data = $_SESSION['cart'];
        foreach ($data as $item) {
            $sum += $item['num'];
        }
        return $sum;
    }

    /**
     * 购物车中商品的总金额
     */
    public function getPrice() {
        //数量为0，价钱为0
        if ($this->getCnt() == 0) {
            return 0.00;
        }
        $price = 0.00;
        $data = $_SESSION['cart'];
        foreach ($data as $item) {
            $price += $item['num'] * $item['price'];
        }
        return sprintf("%01.2f", $price);
    }


    /** 搜索位置 **/

    public function search(){
        $this->display('Public/search');
    }

    /** 底部分类调用**/
    public function footer(){

        if(S(C('HOME_FOOT_MENU')))
        {
            $menulist = S(C('HOME_FOOT_MENU'));
        }else{
            $menulist = R('Service/AllMenu');
            S(C('HOME_FOOT_MENU'),$menulist,3600*24*30);
        }
        $this->assign('footermenu',$menulist);
        $this->display('Public/footer');
    }

}

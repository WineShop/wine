<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;
use Think\Controller;

/**
 * 文档基础模型
 */
class OrderModel extends Model{

   
/*****未完成订单
***************/



/*****所有订单
***************/
public  function allOrder() {
   $uid   = is_login();
   $order = D("order");
   $all=$order->where("display='1' and uid='$uid'  and total!=''")->order("id desc")->select();
   $detail= M("shoplist");
    foreach($all as $n=> $val){
        $all[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();
    }
	return $all; 
}
/*****已完成订单
***************/
public  function finishedOrder() {
	$uid     = is_login();
    $order   = D("order");
    $haslist = $order->where("display='1' and status='3' and uid='$uid'")->order("id desc")->select();
    $detail  = M("shoplist");
    foreach($haslist as $n=> $val){
        $haslist[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();
    }
    return $haslist;
}
/*****最近已完成5笔订单
***************/
public  function latestOrder() {
   $uid    = is_login();
   $order  = M("order");
   $latest = $order->where("display='1' and uid='$uid' and status!=-1 and pricetotal!=''")->limit('5')->order('id desc')->select();
   $detail = M("shoplist");
   foreach($latest as $n=> $val){
       $latest[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();
    }
	return $latest;
}

}

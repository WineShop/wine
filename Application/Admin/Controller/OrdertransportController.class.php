<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | author kevin <lamp365@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台订单控制器
  * @author kevin <lamp365@163.com>
 */
class OrdertransportController extends AdminController {

    /**
     * 订单管理
     * author kevin <lamp365@163.com>
     */
    public function index(){
        /* 查询条件初始化 */
	
        $map  = array('status' => 2);
        $field  = 'id,tag,orderid,pricetotal,create_time,status,uid,display,ispay,total,addressid,message,tool,toolid';
        $list = $this->lists('order', $map,'id desc',$field);

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '订单管理';
        $this->display();
    }

    /**
     * 新增订单
     * @author kevin <lamp365@163.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('order');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info',null);
            $this->display('edit');
        }
    }

    /**
     * 编辑订单
     * @author kevin <lamp365@163.com>
     */
    public function edit(){
        if(IS_POST){
            $Form = D('order');
        
            if($_POST["id"]){
				$id=$_POST["id"];
			    $Form->create();
                $result=$Form->where("id='$id'")->save();
                if($result){
                    //记录行为
                    user_log("管理员修改订单(orderid:{$id})");
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败'.$id);
                }
            } else {
                $this->error('参数有误！');
            }
        } else {
            $id = I('get.id');
            /* 获取数据 */
            $field  = 'id,orderid,tag,pricetotal,create_time,status,assistant,update_time,uid,shipprice,codemoney,display,ispay,total,tool,toolid,addressid';
            $detail = M('order')->field($field)->find($id);

            $field  = 'id,goodid,num,orderid,uid,status,create_time,price,total,sort,tag,parameters';
            $list   = M('shoplist')->where("orderid='$id'")->field($field)->select();

            if(false === $detail){
                $this->error('获取订单信息错误');
            }
            $this->assign('list', $list);
            $this->assign('detail', $detail);

            $this->meta_title = '订单发货';
            $this->display();
        }
    }


    public function del(){
        $order = M("order");
        $shop  = M("shoplist");
        $order->startTrans();
        if(IS_POST){
            $ids   = I('post.id');
            if(is_array($ids)){
                foreach($ids as $id){
                    $res1 = $order->where("id='$id'")->delete();
                    $res2 = $shop ->where("orderid='$id'")->delete();
                }
            }

        }else{
            $id   = I('get.id');
            $res1 = $order->where("id='$id'")->delete();
            $res2 = $shop->where("orderid='$id'")->delete();

        }
        if ($res1 && $res2){
            $order->commit();
            $this->success("删除成功！");
        }else{
            $order->rollback();
            $this->error("删除失败！");
        }
    }

    /*****确认收获 设置 order表status3 shoplis status 3******/
    public function complete(){
        $Form = D('order');
        $uid  = is_login();
        if(I("get.orderid")){
            $orderid = I("get.orderid");
            $Form->startTrans();
            $Form->create();

            $Form->assistant   = $uid;
            $Form->update_time = NOW_TIME;
            $Form->status      = "3";
            $res1 = $Form->where("orderid='$orderid'")->save();

            //根据订单id获取购物清单,设置商品状态为已完成.，status=3
            $res2 = M("shoplist")->where("tag='$orderid'")->save(array('status'=>3,'iscomment'=>1));

            if($res1 && $res2) {
                //记录行为
                $Form->commit();
                user_log("管理员帮用户确认收货(tag:{$orderid})");
                $this->success('确认收货成功', Cookie('__forward__'));
            }else{
                $Form->rollback();
                $this->error('确认收货失败!');
            }

        }else {
            $this->error('参数有误！');
        }

    }

    public function see()
    {
        $id = I('get.id');
        /* 获取数据 */
        $field  = 'id,orderid,tag,pricetotal,create_time,status,assistant,update_time,uid,shipprice,codemoney,display,ispay,total,addressid';
        $detail = M('order')->field($field)->find($id);

        $field  = 'id,goodid,num,orderid,uid,status,create_time,price,total,sort,tag,parameters';
        $list   = M('shoplist')->where("orderid='$id'")->field($field)->select();

        if(false === $detail){
            $this->error('获取订单信息错误');
        }
        $this->assign('list', $list);
        $this->assign('detail', $detail);

        $this->meta_title = '订单发货';
        $this->display();
    }

    public function kuaidi()
    {
        ppd('快递查询，等待开发');
    }

}
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
class CancelagreeController extends AdminController {

    /**
     * 订单管理
     * author kevin <lamp365@163.com>
     */
    public function index(){
        /* 查询条件初始化 */
	
       $map    = array('status' =>3);
        $field = 'id,goodid,num,orderid,create_time,reason,shopid,refuse_info,status,info,cash,count,parameters';
       $list   = $this->lists('cancel', $map,'id desc',$field);

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->meta_title = '退货管理';
        $this->display();
    }

    /**
     * 新增订单
     * @author kevin <lamp365@163.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('cancel');
            $Config->create();
			  /* 新增时间并更新时间*/
             $Config->time = NOW_TIME;
            $Form->update_time = NOW_TIME;
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
            $this->display();
        }
    }

    /**
     * 编辑订单
     * @author kevin <lamp365@163.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Form = D('cancel');
         
            if($_POST["id"]){
				$id=$_POST["id"];
           $Form->create();
			    /*更新时间*/
            $Form->update_time = NOW_TIME;
			/* 编辑后保存编辑人*/
           $result=$Form->where("id='$id'")->save();
                if($result){
                    //记录行为
                    action_log('update_cancel', 'cancel', $data['id'], UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败55'.$id);
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('cancel')->find($id);
            $orderid=$info["orderid"];
            $list=M('order')->where("orderid='$orderid'")->select();
			$detail=M('shoplist');
            foreach($list as $n=> $val){
            $list[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select(); 
     }
     $trans=M('transport');
	   foreach($list as $k=> $va){
         $list[$k]['addressid']=$trans->where('id=\''.$va['addressid'].'\'')->select(); 
     }
            if(false === $info){
                $this->error('获取订单信息错误');
            }
            $this->assign('list', $list);

			 $this->assign('info', $info);
            $this->meta_title = '编辑订单';
            $this->display();
        }
    }

 
   /**
     * 删除订单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
       if(IS_POST){
             $ids = I('post.id');
            $order = M("cancel");
			
            if(is_array($ids)){
                             foreach($ids as $id){
		
                             $order->where("id='$id'")->delete();
						
                }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("cancel");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }



}
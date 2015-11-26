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
class PayController extends AdminController {

    /**
     * 订单管理
     * author kevin <lamp365@163.com>
     */
    public function index(){
        /* 查询条件初始化 */

       $field = 'id,out_trade_no,money,status,type,uid,total,yunfee,ratio,ratioscore,create_time,update_time';
       $list   = $this->lists('pay','','id desc',$field);

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
            $Config = D('pay');
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
    public function edit($id = 0){
        if(IS_POST){
            $Form = D('pay');
          $uid=is_login();
            if($_POST["id"]){ 
                $Form->create();
				$id=$_POST["id"];
				$Form->update_time = NOW_TIME;
			$Form->assistant = $uid;
           $result=$Form->where("id='$id'")->save();
                if($result){
                    //记录行为
                    user_log("管理员修改订单(id:{$id})");
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败55'.$id);
                }
            } else {
                $this->error("参数有误！");
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('pay')->find($id);
$detail= M('pay')->where("id='$id'")->select();
$codeid=M('pay')->where("id='$id'")->getfield("out_trade_no");
$oid=M('order')->where("orderid='$codeid'")->getfield("id");
$list=M('shoplist')->where("orderid='$id'")->select();

            if(false === $info){
                $this->error('获取订单信息错误');
            }
$this->assign('list', $list);
            $this->assign('detail', $detail);
			 $this->assign('info', $info);
			 $this->assign('a', $id);
            $this->meta_title = '编辑订单';
            $this->display();
        }
    }
 
   /**
     * 删除
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
       if(IS_POST){
             $ids = I('post.id');
            $pay = M("pay");
			
            if(is_array($ids)){
                             foreach($ids as $id){
		
                             $pay->where("id='$id'")->delete();
							 
                }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("pay");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }




}
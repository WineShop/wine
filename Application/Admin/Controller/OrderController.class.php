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
class OrderController extends AdminController {

    /**
     * 订单管理
     * author kevin <lamp365@163.com>
     */
    public function index(){
        /* 查询条件初始化 */
        $map    = array('status' => 1);
        $field  = 'id,tag,orderid,pricetotal,create_time,status,uid,display,ispay,total,addressid,message';
        $list   = $this->lists('order', $map,'id desc',$field);

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
 * 订单发货 修改订单状态为2 并对库存减去和售出累加
 * @author kevin <lamp365@163.com>
 */
    public function send($id = 0){
        if(IS_POST){
            $Form = D('order');
            $uid  = is_login();
            if($_POST["id"]){
				$id = $_POST["id"];
                //开启事务
                $Form->startTrans();
                $Form->create();
                $Form->assistant   = $uid;
                $Form->update_time = NOW_TIME;
                $Form->tool        = I('post.tool');
                $Form->toolid      = I('post.toolid');
                $Form->send_name   = I('post.send_name');
                $Form->send_contact= I('post.send_contact');
                $Form->send_address= I('post.send_address');
                $Form->status      = 2;

                $result = $Form->where("id='$id'")->save();

                //根据订单id获取购物清单
                $shop_info = M("shoplist")->where("orderid='$id'")->field('id,goodid,num')->select();

                foreach($shop_info as $k=>$val)
                {
                    //获取购物清单数据表产品id，字段id
                    $byid      = $val["id"];
                    $goodid    = $val["goodid"];
                    $total_num = "`total_num`+{$val['num']}";
                    //销量加1 库存减1
                    $setdata = array(
                        'sale'           => array('exp', '`sale`+1'),
                        'total_num'      => array('exp', $total_num),
                    );
                    $sales = M('document')->where("id='$goodid'")->save($setdata);
                }

                $data['status']=2;
                $res = M("shoplist")->where("orderid='$id'")->save($data);

                if($result && $res){
                    $Form->commit();
                    //记录行为
                    user_log("管理员确认了发货(orderid:{$id})");
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $Form->rollback();
                $this->error('参数有误！');
            }
        } else {
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
    }



    public function del(){
        if(IS_POST){
            $ids   = I('post.id');
            $res   = delUserOrder($ids);
        }else{
            $id   = I('get.id');
            $res  = delUserOrder($id);
        }
        if($res)
            $this->success("删除成功！");
        else
            $this->error("删除失败！");
    }

    public function see()
    {
        $id    = I('get.id');
        $data = seeUserOrderDetail($id);
        if(!$data)
            $this->error('获取订单信息错误');
        $this->assign('list', $data['list']);
        $this->assign('detail', $data['detail']);

        $this->meta_title = '订单发货';
        $this->display();
    }


    public function ajaxGetAddress()
    {
        $id   = I('post.addressid');
        $data = array();
        if(!empty($id)){
            $data = get_address_by_id($id);
        }
        $this->ajaxReturn($data);
    }

}
<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class OrderController extends HomeController {

    /* 文档模型频道页 */
    public function detail(){
        $uid     = $this->login();
        $id      = I('get.id');//获取id
        $typeCom = M("order")->where("orderid='$id'")->getField("tool");
        $typeNu  = M("order")->where("orderid='$id'")->getField("toolid");
        if(isset($typeCom)&&$typeNu){
            $retData = $this->getkuaidi($typeCom,$typeNu );
        }else{
            $retData = "";
        }
        $this->assign('kuaidata', $retData);

        /* 购物车调用*/
        $cart  =  $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $order  = D("Order");
        $detail = $order->where("orderid='$id'")->select();

        $list=M("shoplist");
        foreach($detail as $n=> $val){
            $detail[$n]['id']=$list->where('orderid=\''.$val['id'].'\'')->select();

        }

        $addressid=$order->where("orderid='$id'")->getField("addressid");
        $trans=M("transport")->where("id='$addressid'")->select();

        $this->assign('translist',$trans);
        $this->assign('detaillist',$detail);
        $this->meta_title = '订单详情';
        $this->display();
    }

    /* 取消订单 */
    public function cancel(){
        $uid = $this->login();
        $this->meta_title = '取消订单';

        /* 购物车调用*/
        $cart  =  $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);


        if(IS_POST){
            $id       = I('post.id');//获取orderid
            $order    = M("order");
            $shoplist = M('shoplist');
            $info     = $order->where(array("orderid"=>$id,'uid'=>$uid))->field('status,ispay,id')->find();
            $status   = $info["status"];
            $num      = $info["ispay"];
            $orderid  = $info["id"];
            $data     = $shoplist->where("orderid='$orderid'")->field('num,price')->select();
            if(empty($data)){
                $this->ajaxError('取消有误，非法参数！');
            }
            $cash = 0;
            foreach ($data as $k=>$val) {
                /*取消的商品总额*/
                $cash += $val['num'] * $val['price'];
                /*退货中的商品件数*/
                $shop_num +=$val['num'];
                /*退货中的商品种类数*/
                $count += 1;

            }

            //订单已提交或未支付直接取消   货到付款 已提交  || 在线支付未完成 待支付
            if(($num==-1&&$status==1)||($num==1&&$status==-1)){
                //设置订单取消

                //保存数据到取消表中后台调用
                $cancel=D("cancel");
                $cancel->create();
                $cancel->create_time = NOW_TIME;
                $cancel->status  = 3;
                $cancel->orderid = $id;
                $cancel->cash    = $cash;//取消的金额
                $cancel->num     = $shop_num;//取消的数量
                $cancel->count   = $count;//取消的种类
                $cancel->info    = "自助取消";
                $cancel ->add();
                //设置订单为订单已取消
                $data = array('status'=>'6','backinfo'=>'订单已关闭');
                //更新订单列表订单状态为已取消，清空取消订单操作
                if($order->where("id='{$orderid}'")->setField($data)) {
                    $this->ajaxSuccess('申请成功，订单已取消');
                }else{
//                    \Think\LogTool::instance()->setLogger('Ucenter/order');
//                    \Think\LogTool::instance()->setLog('error',$uid.'用户在'.date('Y-h-d H:i:s',time()).' 订单取消失败了：');
                    $this->ajaxError('申请失败，请重试');
                }
            }else{  //订单已发货，或已支付未发货,需申请，申请状态码4，拒绝5，同意6
                $cancel=D("cancel");
                $cancel->create();
                $cancel->time=NOW_TIME;
                $cancel->status=1;
                $cancel->orderid=$id;
                $cancel->cash  = $cash;//取消的金额
                $cancel->num   = $shop_num;//取消的数量
                $cancel->count = $count;//取消的种类
                $cancel ->add();
                $data = array('status'=>'4');//设置订单状态为已提交，发货等状态不变
                if($order->where("id='{$orderid}'")->setField($data)) {
                    $this->ajaxSuccess('申请成功，你可以重亲购物');
                }else{
//                    \Think\LogTool::instance()->setLogger('Ucenter/order');
//                    \Think\LogTool::instance()->setLog('error',$uid.'用户在'.date('Y-h-d H:i:s',time()).' 订单取消失败了：');
                    $this->ajaxError('申请失败，请重试!');
                }

            }
        }else{   //is_post
            $id    = I('get.id');//获取orderid
            $msg   = "申请取消订单:";
            $order = M("order");
            $detail= $order->where(array('uid'=>$uid,'orderid'=>$id))->field('status,ispay')->find();
            if(empty($detail))
                $this->error('该订单不存在！');

            $num  = $detail["status"];

            if($num == "1"){
                $paynum = $detail["ispay"];

                if($paynum == "1"){
                    $info="当前订单状态为未完成支付";
                }
                if(!$paynum){
                    $info="当前订单已提交等待发货中";
                }
            }
            if($num == "2")
                $info="当前提交的订单已发货,需审核通过后取消";

            if($num == '-1')
                $info="当前订单的状态为待支付";

            $this->assign('info',$info);
            $this->assign('id',$id);
            $this->assign('msg',$msg);
            $this->display();
            $this->meta_title = '取消订单';
        }

    }

    public function canceldetail(){
        $uid = $this->login();
        /* 购物车调用*/
        $cart  =  $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $orderid= I('get.id');//获取orderid
        $cancel=M("cancel");
        $list= $cancel->where("orderid='$orderid'")->select();
        $info= M("order")->where("orderid='$orderid'")->getField("backinfo");
        $this->assign('list',$list);
        $this->assign('backinfo',$info);
        $this->assign('id',$orderid);
        $msg="申请取消订单:";
        $this->meta_title = '取消订单详情';
        $this->assign('msg',$msg);
        $this->display();

    }
    public function backdetail(){
        $uid = $this->login();
        /* 左侧菜单 */
        $this->meta_title = '退货详情';
        /* 购物车调用*/
        $cart  =  $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $id= I('get.id');//获取orderid
        $back=M("backlist");
        $list= $back->where("shopid='$id'")->find();
        $info= M("backlist")->where("shopid='$id'")->getField("backinfo");
        $this->assign('info',$list);
        $this->assign('backinfo',$info);
        $msg="退货单";
        $this->assign('msg',$msg);
        $this->display();

    }

    public function changedetail(){
        $uid = $this->login();
        /* 购物车调用*/
        $cart  =  $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $id= I('get.id');//获取id
        $change=M("change");
        $list= $change->where("shopid='$id'")->find();
        $info= M("change")->where("shopid='$id'")->getField("backinfo");
        $this->assign('info',$list);
        $this->assign('backinfo',$info);
        $this->assign('id',$id);
        $this->meta_title = '换货单'.$list['id'].'详情';

        $msg="换货:";
        $this->assign('msg',$msg);
        $this->display();

    }
    public function wuliu(){
        $uid = $this->login();
        $id= I('get.orderid');//获取id
        $this->meta_title = '订单'.$id.'物流详情';
        $typeCom=M("order")->where("orderid='$id'")->getField("tool");
        $typeNu=M("order")->where("orderid='$id'")->getField("toolid");

        if(isset($typeCom)&&$typeNu){
            $retData=$this->getkuaidi($typeCom,$typeNu );
        }
        else{$retData="";
        }
        $this->assign('kuaidata', $retData);
        /* 购物车调用*/
        $cart  =  $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        $this->display();

    }

    public function back(){
        $uid = $this->login();
        if(IS_POST){
            $id= I('post.id');//获取id
            $num= I('post.num');//获取num
            $price= I('post.price');//获取价格
            $goodid= I('post.goodid');//获取goodid
            $parameters = I('post.parameters');//获取parameters
            $datanum=M("shoplist")->where("id='$id'")->getField("num");

            if($datanum<$num){
                $this->error('超出购买数量');
            }else{
                //保存信息到退货表
                $back= D("backlist");

                $back->create();//Create方法创建的数据对象是保存在内存,并没有实际写入到数据库，直到使用add或者save方法才会真正写入数据库
                $back->create_time=NOW_TIME;
                $back->status=1;
                $back->total=$num*$price;
                $back->parameters=$parameters;
                $result=$back->add();

                //更改商品的售后信息
                $data['status']=4;
                $shop=M("shoplist");
                $add=$shop->where("id='$id'")->save($data);
                if($add) {
                    $this->success('提交成功',U("center/index"));
                }else{
                    $this->error('申请失败',U("center/index"));
                }
            }

        }else{  //is_post
            $this->meta_title = '提交退货单';
            $id= I('get.id');//获取id
            $msg="Tips，提交退货单";
            $detail=M("shoplist")->find($id);
            //获取购物清单
            $this->assign('list',$detail);
            //获取物品id
            $this->assign('id',$id);
            $this->assign('msg',$msg);
            $this->display();

        }


    }
    public function backkuaidi(){
        $uid = $this->login();
        if(IS_POST){
            $id= I('post.backid');//获取退货主键id
            $back=D("backlist");
            $shopid=$back->where("id='$id'")->getField("shopid");
            //保存信息到退货表

            $back->create();//Create方法创建的数据对象是保存在内存,并没有实际写入到数据库，直到使用add或者save方法才会真正写入数据库
            $back->status=4;

            $result=$back->where("id='$id'")->save();
            //更改商品的售后信息
            $data['status']=6;
            $shop=M("shoplist");
            $add=$shop->where("id='$shopid'")->save($data);
            if($add)
            {
                $this->success('提交成功',U("center/index"));
            }
            else{
                $this->error('申请失败');
            }
        }else{
            $id= I('get.id');//获取id
            $msg="Tips，提交退货单";
            $this->meta_title = '填写退货单';
            $detail=M("shoplist")->find($id);
            //获取购物清单
            $this->assign('info',$detail);
            //获取物品id
            $this->assign('id',$id);
            $this->assign('msg',$msg);
            $this->display();

        }


    }
    public function change(){
        $uid = $this->login();
        if(IS_POST){
            $id= I('post.id');//获取id
            $num= I('post.num');//获取num
            $price= I('post.price');//获取价格
            $parameters = I('post.parameters');//获取parameters
            $change=D("change");
            $change->create();
            $change->create_time=NOW_TIME;
            $change->total=$num*$price;
            $change->status=1;
            $back->parameters=$parameters;
            $change->add();
            //更改商品的售后信息
            $data['status']=-4;
            $shop=M("shoplist");
            if($shop->where("id='$id'")->save($data)) {
                $this->success('申请成功',U("center/index"));
            }else{
                $this->error('申请失败，或重复操作');
            }
        }else{
            $id= I('get.id');//获取id
            $msg="Tips，提交换货单";
            $this->meta_title = '填写换货单';
            $detail=M("shoplist")->find($id);
            //获取购物清单
            $this->assign('list',$detail);

            $this->assign('msg',$msg);

            $this->display();

        }

    }

    public function changekuaidi(){
        $uid = $this->login();
        if(IS_POST){
            $id= I('post.backid');//获取退货主键id
            $back=D("change");
            $shopid=$back->where("id='$id'")->getField("shopid");
            //保存信息到退货表

            $back->create();//Create方法创建的数据对象是保存在内存,并没有实际写入到数据库，直到使用add或者save方法才会真正写入数据库
            $back->status=4;
            $result=$back->where("id='$id'")->save();
            //更改商品的售后信息
            $data['status']=-6;
            $shop=M("shoplist");
            $add=$shop->where("id='$shopid'")->save($data);
            if($add)
            {
                $this->success('提交成功',U("center/index"));
            }else{
                $this->error('申请失败');
            }
        }else{
            $id= I('get.id');//获取id
            $msg="Tips，提交退货单";
            $this->meta_title = '换货快递操作';
            $detail=M("shoplist")->find($id);
            //获取购物清单
            $this->assign('info',$detail);
            //获取物品id
            $this->assign('id',$id);
            $this->assign('msg',$msg);
            $this->display();

        }


    }
    public function getkuaidi($typeCom,$typeNu ){

//$typeCom = $_GET["com"];//快递公司
        //$typeNu = $_GET["nu"];  //快递单号

//echo $typeCom.'<br/>' ;
//echo $typeNu ;

        $AppKey=C('100KEY');//请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY
        $url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';

//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
        $powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';


//优先使用curl模式发送数据
        if (function_exists('curl_init') == 1){
            $curl = curl_init();
            curl_setopt ($curl, CURLOPT_URL, $url);
            curl_setopt ($curl, CURLOPT_HEADER,0);
            curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
            curl_setopt ($curl, CURLOPT_TIMEOUT,5);
            $get_content = curl_exec($curl);
            curl_close ($curl);
        }else{
            Vendor("Snoopy.Snoopy");
            $snoopy=new \Vendor\Snoopy\Snoopy();
            $snoopy->referer = 'http://www.google.com/';//伪装来源
            $snoopy->fetch($url);
            $get_content = $snoopy->results;
        }
        return $get_content;
//print_r($get_content . '<br/>' . $powered);


    }
    public function complete($id = 0){
        $uid = $this->login();
        if(IS_GET){
            $Form = D('order');
            $id=$_GET["id"];
            $Form->create();
            $Form->status="3";
            $Form->where("orderid='$id'")->save();
            $dataid=$Form->where("orderid='$id'")->getField("id");
            //根据订单id获取购物清单,设置商品状态为已完成.，status=3
            $array=M("shoplist")->where("orderid='$dataid'")->select();

            foreach($array as $k=>$val)
            {
                //获取购物清单数据表产品id，字段id
                $byid=$val["id"];
                M("shoplist")->where("id='$byid'")->setField("status","3");
                M("shoplist")->where("id='$byid'")->setField("iscomment","1");
            }
            if($dataid){
                //记录行为
                action_log('update_order', 'order', $data['id'], UID);
                $this->success('确认收货成功，您可以返回评价商品', Cookie('__forward__'));
            }else {
                $this->error('确认收货失败'.$id);
            }
        }

    }

}

<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 1010422715@qq.com  All rights reserved.
// +----------------------------------------------------------------------
// |  Author: 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
/*****个人中心
 ***************/
class CenterController extends HomeController {

    /* 会员中心首页*/
    public  function index() {
        if(is_login()){
            /** 购物车中数量**/
            $shopnum  = empty($_SESSION['cart']) ? 0 : count($_SESSION['cart']);
            $this->assign('shopnum', $shopnum);

            /** 热词调用 热门搜索**/
            $hotsearch = C('HOT_SEARCH');
            $this->assign('hotsearch',$hotsearch);

            $uid = D("Member")->uid();
            $this->assign('uid', $uid);

            /*****最近订单
             ***************/
            /* 数据分页*/
            $Member = D("Member");
            $order  = M("order");
            $detail = M("shoplist");
            $count  = $order->where(" uid='$uid'")->count();
            $this->assign('anum', $count);
            $Page= new \Think\Page($count,5);
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','尾页');
            $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $show = $Page->show();
            $list = $order->where("uid='$uid'")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $n=> $val){
                $list[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();
            }
            $this->assign('allorder',$list);// 赋值数据集
            $this->assign('page',$show);//

            $onum  = 0; //待支付
            $dnum  = 0; //待发货
            $cnum  = 0; //待确认
            if(!empty($list))
            {
                if(count($list) < $count)
                {
                    $list = $order->where("uid='$uid'")->field('id,status,ispay')->select();
                }

                foreach($list as $row)
                {
                    if($row['status'] == 1)
                        $dnum ++;
                    else if($row['status'] == 2)
                        $cnum ++;
                    else if($row['status'] == -1 && $row['ispay'] == 1)
                        $onum ++;
                }
            }
            $this->assign('onum', $onum);
            $this->assign('dnum', $dnum);
            $this->assign('cnum', $cnum);

            /*****收藏夹
             ***************/
            $fav    = D("Favortable");
            $favor  = $fav->getfavor();
            $this->assign('favorlist', $favor);
            $faceid = M('ucenter_member')->where("id='$uid'")->getField("face");
            $this->assign('faceid', $faceid);

            /*优惠券数量*/
            $num = M("usercoupon")->where("uid='$uid'")->count();
            $this->assign('num', $num);


            /*用户信息*/
            $ucache = D("Member")->getUserCache();
            $this->assign('ucache', $ucache);
            //站内信数量
            $condition['uid']=$uid;
            $condition['group']=2;
            $condition['status']=1;
            $ecount=M("personenvelope")->where($condition)->count();
            $this->assign('ecount', $ecount);
            $this->meta_title = get_username().'的个人中心';
            $this->display();
        }else {
            $this->redirect('User/login');
        }

    }

    public  function reason() {
        $this->display();

    }

    /*****用户签到
     ***************/
    public  function enter() {
        $uid     = D('Member')->uid();
        $iswork  = D("iswork");
        $qtime   = NOW_TIME;
        $d       = date('H:i:s',$qtime);
        $time    = $iswork->where("uid='$uid'")->order("id desc")->limit(1)->getfield('create_time');
        $member  = D("Member"); // 实例化对象
        if($time){/*签过到*/
            $a  = date('Ymd',$qtime);/*格式时间戳为 20141024*/
            $b  = date('Ymd',$time);
            $c  = date('H:i:s',$time);
            if($a!==$b){/*比较*/
                /*新增签到*/
                $data['uid']           = $uid;
                $data['status']        = "1";
                $data['create_time']   = NOW_TIME;
                if($iswork->add($data))
                {
                    $member->where("uid='$uid'")->setInc('score',10); // 用户的积分加10
                    $msg = "已签到，积分+10";
                    $this->ajaxSuccess($msg);
                }

            }else{/*签过到*/
                $msg  = "今天".$c."已签过到";
                $this->ajaxError($msg);
            }

        }else{/*首次签到*/
            $data['uid']            = $uid;
            $data['status']         = "1";
            $data['create_time']    = NOW_TIME;
            $member->where("uid='$uid'")->setInc('score',10); // 用户的积分加10

            if($iswork->add($data))
            {
                $msg = "已签到，积分+10,签到时间：".$d;
                $this->ajaxSuccess($msg);
            }
        }
    }
    /***站内信***/
    public  function envelope() {   /* 购物车调用*/
        /* 购物车调用*/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $table=D("personenvelope");
        $Member=D("member");
        $uid=$Member->uid();
        $condition['uid'] = $uid;
        $condition['group'] ="2";
        $condition['username'] =get_regname($uid);
        $condition['_logic'] = 'OR';
        $count=$table->where($condition)->count();
        $Page= new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $list=$table->where($condition)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page',  $show);
        $this->meta_title = '我的收藏';
        $this->display();
    }
    /***站内信读取***/
    public  function msg() {
        /* 购物车调用*/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $envelope= M("personenvelope");
        $uid=D("member")->uid();
        $id=I("get.id");
        /* 更新浏览数 */
        $map = array('id' => $id);
        $envelope->where($map)->setInc('view');
        $list=$envelope->find($id);
        $envelope->where($map)->setField("status",2);
        $this->assign("list",$list);
        $this->meta_title = '查看站内信';
        $this->display();
    }
    /**
     * 获取用户uid
     */
    public  function uid() {
        $uid=D("member")->uid();
        return $uid;

    }
    /**
     * 全部订单
     */
    public  function allorder(){
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 数据分页*/
        $Member=D("member");
        $uid=$Member->uid();
        $order=M("order");
        $detail=M("shoplist");
        $count=$order->where(" uid='$uid'  and total!=''")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $list=$order->where("uid='$uid'  and total!=''")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $n=> $val){
            $list[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();}
        $this->assign('allorder',$list);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '我的所有订单';
        $this->display();
    }

    /* 待支付订单*/
    public  function needpay(){
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 数据分页*/
        $Member=D("member");
        $uid=$Member->uid();
        $order=M("order");
        $detail=M("shoplist");
        $count=$order->where("uid='$uid' and status='-1' and ispay='1'")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $list=$order->where("uid='$uid' and status='-1' and ispay='1'")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $n=> $val){
            $list[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();}
        $this->assign('needpay',$list);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '待支付订单';
        $this->display();
    }


    /* 待发货订单*/
    public  function tobeshipped(){
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 数据分页*/
        $Member=D("member");
        $uid=$Member->uid();
        $order=M("order");
        $detail=M("shoplist");
        $count=$order->where("uid='$uid' and status='1' ")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $list=$order->where("uid='$uid' and status='1' ")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $n=> $val){
            $list[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();}
        $this->assign('tobeshipped',$list);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '待发货订单';
        $this->display();
    }
    /* 待发货订单*/
    public  function tobeconfirmed(){
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        /* 数据分页*/
        $Member=D("member");
        $uid=$Member->uid();
        $order=M("order");
        $detail=M("shoplist");
        $count=$order->where("uid='$uid' and status='2' ")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $list=$order->where("uid='$uid' and status='2' ")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $n=> $val){
            $list[$n]['id']=$detail->where('orderid=\''.$val['id'].'\'')->select();}
        $this->assign('tobeconfirmed',$list);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '待发货订单';
        $this->display();
    }

    /*****购物车
     ***************/
    public  function shopcart() {

        /* 底部分类调用*/
        $menulist=R('Service/AllMenu');
        $this->assign('footermenu',$menulist);
        $uid=$this->uid();
        $cart=D("shopcart");
        $cartlist=$cart->getcart();
        return $cartlist;
    }
    /*****收藏夹
     ***************/
    public  function collect() {   /* 购物车调用*/
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $table=D("favortable");  $Member=D("member");
        $uid=$Member->uid();
        $count=$table->where(" uid='$uid' ")->count();
        $Page= new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $favorlist=$table->where("uid='$uid' ")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('favorlist', $favorlist);
        $this->assign('page',  $show);
        $this->meta_title = '我的收藏';
        $this->display();
    }
    public  function coupon() {
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 会员调用*/
        $member=D("member");
        $uid=$member->uid();
        /* 优惠券调用*/
        $coupon=M("usercoupon")->where("uid='$uid' ")->select();
        $this->assign('couponlist', $coupon);
        $fcoupon=M("fcoupon")->where("display='1' and status='1' ")->select();;
        $this->assign('fcouponlist', $fcoupon);
        $this->meta_title = '我的优惠券';$this->display();
    }

    /*****个人资料
     ***************/
    public  function information() {   /* 购物车调用*/
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $uid=$this->uid();
        $order=D("member");
        $faceid=M('ucenter_member')->where("id='$uid'")->getField("face");
        $this->assign('faceid', $faceid);

        $ucenter=$order->where("uid='$uid'")->select();
        $this->meta_title = get_username().'个人中心';
        $this->assign('information', $ucenter);
        $this->display();
    }


    public  function comment() {   /* 购物车调用*/
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $comment=D("comment");
        $Member=D("member");
        $uid=$Member->uid();
        $count=$comment->where(" uid='$uid' ")->count();
        $Page= new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show= $Page->show();
        $commentlist=$comment->where("uid='$uid' ")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('comment', $commentlist);
        $this->assign('page',  $show);
        $this->meta_title = get_username().'的评论管理';
        /* 差评*/
        $bad = M("comment")->where("status='1' and uid='$uid' and score='1'")->count();
        $common = M("comment")->where("status='1' and uid='$uid' and score='2'")->count();
        $best = M("comment")->where("status='1' and uid='$uid' and score='3'")->count();
        $this->assign('bad', $bad);
        $this->assign('common',$common);
        $this->assign('best',$best);
        $this->display();
    }

    public  function update() {
        $m=D("member");
        $uid=$m->uid();
        $member=M("ucenter_member");
        $data = $member->create();
        $result =$member->where("id='$uid'")->save();
        if($result) {
            $this->success('修改成功！',U("center/information"));
        }else{
            $this->error('修改失败！');
        }


    }
    public  function address() {
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $m=D("member");
        $uid=$m->uid();
        if(IS_POST){

        }else{
            $address=M("transport");
            $list=$address->where("uid='$uid'")->select();
            $this->assign('list', $list);
            $this->meta_title = get_username().'的地址管理';

            $this->display();
        }

    }
    public  function shezhi() {

        if(IS_AJAX){
            $m=D("member");
            $uid=$m->uid();
            $Transport = M("transport"); // 实例化transport对象
            $data['status'] = 0;
            $Transport->where("uid='$uid'")->save($data);
            $id=$_POST["id"];
            $result=$Transport->where("uid='$uid' and id='$id'")->setField("status",1);
            if($result){
                $msg = "设置成功";
                $this->ajaxreturn($msg);
            }else{
                $msg = "设置失败";
                $this->ajaxreturn($msg);
            }
        }
    }

// 增加地址
    public  function save() {
        $Transport = M("transport"); // 实例化transport对象
        $data['address'] = $_POST["posi"];
        $data['cellphone'] = $_POST["pho"];
        $data['realname'] = $_POST["rel"];
        $Member=D("member");
        $uid=$Member->uid();
        $data['uid'] = $uid;
        $data['status'] = 0;
        $data['time']=NOW_TIME;

        if($Transport->add($data)){
            $id=$Transport->where("uid='$uid'")->limit(1)->order("id desc")->getField("id");
            $this->ajaxreturn($id);
        }else{
            $this->ajaxreturn($data);
        }
    }

// 删除地址
    public  function deleteAddress() {
        $Transport = M("transport"); // 实例化transport对象
        $Member=D("member");
        $uid=$Member->uid();
        $id=$_POST["id"];
        if($Transport->where("uid='$uid' and id='$id'")->delete()){
            $data['msg'] = "删除成功";
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['msg'] = "删除失败";
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

}

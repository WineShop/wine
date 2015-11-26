<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 791845283@qq.com  All rights reserved.
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
/*****个人中心
 ***************/
class CenterController extends HomeController {

    /* 会员中心首页*/
    public  function index() {
        $uid = $this->login();
        $this->assign('uid', $uid);

        /** 购物车中数量**/
        $shopnum  = empty($_SESSION['cart']) ? 0 : count($_SESSION['cart']);
        $this->assign('shopnum', $shopnum);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /*****最近订单**************/
        /* 数据分页*/
        $order  = M("order");
        $count  = $order->where(" uid='$uid'")->count();
        $this->assign('anum', $count);

        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show  = $Page->show();
        $field = 'id,orderid,tag,pricetotal,create_time,status,uid,display,ispay,total,backinfo';
        $list  = $order->where("uid='$uid'")->field($field)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        /********通过order订单id,获取shoplist物品以及document的信息*********/
        $data = $this->shoplistAndDocumentByTag($list);
        $this->assign('allorder',$data);// 赋值数据集
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


        $uface = M('ucenter_member')->where("id='$uid'")->getField("face");
        $this->assign('uface', $uface);

        /*优惠券数量*/
        $num = M("usercoupon")->where("uid='$uid'")->count();
        $this->assign('num', $num);


        /*用户信息*/
        $ucache = getUserCache();
        $this->assign('ucache', $ucache);

        //站内信数量
        $condition['uid']=$uid;
        $condition['group']=2;
        $condition['status']=1;
        $ecount=M("personenvelope")->where($condition)->count();
        $this->assign('ecount', $ecount);
        $this->meta_title = $ucache['username'].'的个人中心';
        $this->display();

    }

    public  function reason() {
        $this->display();
    }

    /*****用户签到***************/
    public  function enter() {
        $uid     = $this->login();
        $iswork  = D("iswork");
        $qtime   = NOW_TIME;
        $d       = date('H:i:s',$qtime);
        $time    = $iswork->where("uid='$uid'")->order("id desc")->limit(1)->getfield('create_time');
        $member  = D("ucenter_member"); // 实例化对象
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
                    $member->where("id='$uid'")->setInc('score',10); // 用户的积分加10
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
            $member->where("id='$uid'")->setInc('score',10); // 用户的积分加10

            if($iswork->add($data))
            {
                $msg = "已签到，积分+10,签到时间：".$d;
                $this->ajaxSuccess($msg);
            }
        }
    }
    /***站内信***/
    public  function envelope() {
        $uid = $this->login();

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $user                  = getUserCache();
        $table                 =  D("personenvelope");
        $condition['uid']      = $uid;
        $condition['group']    = "2";
        $condition['username'] = $user['username'];
        $condition['_logic'] = 'OR';
        $count = $table->where($condition)->count();
        $Page = new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show  = $Page->show();
        $field = 'id,sendname,title,content,status,uid,view,create_time,update_time,group';
        $list  = $table->where($condition)->field($field)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page',  $show);
        $this->meta_title = '站内信';
        $this->display();
    }
    /***站内信读取***/
    public  function msg() {
        $uid = $this->login();
        /* 购物车调用*/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $envelope= M("personenvelope");
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
        return is_login();

    }
    /**
     * 全部订单
     */
    public  function allorder(){
        $uid = $this->login();
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 数据分页*/
        $order = M("order");
        $count = $order->where(" uid='$uid'  and total!=''")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show = $Page->show();
        $list = $order->where("uid='$uid'  and total!=''")->order('id desc')->field('id,orderid,tag,pricetotal,create_time,status,ispay,total')
                      ->limit($Page->firstRow.','.$Page->listRows)->select();

        /********通过order订单id,获取shoplist物品以及document的信息*********/
        $data = $this->shoplistAndDocumentByTag($list);

        $this->assign('allorder',$data);// 赋值数据集

        $this->assign('page',$show);// 
        $this->meta_title = '我的所有订单';
        $this->display();
    }

    /* 待支付订单*/
    public  function needpay(){
        $uid = $this->login();
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 数据分页*/
        $order=M("order");
        $count=$order->where("uid='$uid' and status='-1'")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show  = $Page->show();
        $field = 'id,orderid,tag,pricetotal,create_time,status,uid,display,ispay,total,backinfo';
        $list  = $order->where("uid='$uid' and status='-1'")->field($field)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        /********通过order订单id,获取shoplist物品以及document的信息*********/
        $data = $this->shoplistAndDocumentByTag($list);
        $this->assign('allorder',$data);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '待支付订单';
        $this->display();
    }


    /* 待发货订单*/
    public  function tobeshipped(){
        $uid = $this->login();
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 数据分页*/
        $order=M("order");
        $count=$order->where("uid='$uid' and status='1' ")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show  = $Page->show();
        $field = 'id,orderid,tag,pricetotal,create_time,status,uid,display,ispay,total,backinfo';
        $list  = $order->where("uid='$uid' and status='1'")->field($field)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        /********通过order订单id,获取shoplist物品以及document的信息*********/
        $data = $this->shoplistAndDocumentByTag($list);
        $this->assign('allorder',$data);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '待发货订单';
        $this->display();
    }
    /* 待确认订单 已经发货*/
    public  function tobeconfirmed(){
        $uid = $this->login();
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        /* 数据分页*/
        $order=M("order");
        $count=$order->where("uid='$uid' and status='2' ")->count();
        $Page= new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','第一页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show  = $Page->show();
        $field = 'id,orderid,tag,pricetotal,create_time,status,uid,display,ispay,total,backinfo';
        $list  = $order->where("uid='$uid' and status='2'")->field($field)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        /********通过order订单id,获取shoplist物品以及document的信息*********/
        $data = $this->shoplistAndDocumentByTag($list);
        $this->assign('allorder',$data);// 赋值数据集
        $this->assign('page',$show);// 
        $this->meta_title = '待发货订单';
        $this->display();
    }

    /*****购物车
     ***************/
    public  function shopcart() {
        $cartlist = $_SESSION['cart'];
        return $cartlist;
    }
    /*****收藏夹
     ***************/
    public  function collect() {   /* 购物车调用*/
        $uid = $this->login();

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $table=D("Favortable");
        $count=$table->where(" uid='$uid' ")->count();

        $favorlist = '';
        $show      = '';
        if($count>0){
            $Page= new \Think\Page($count,10);
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','尾页');
            $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $show = $Page->show();
            $field = 'goodid';
            $data = $table->where("uid='$uid' ")->order('id desc')->field($field)->limit($Page->firstRow.','.$Page->listRows)->select();

            $aIds = array();
            foreach($data as $arr)
            {
                if(!in_array($arr['goodid'],$aIds))
                    $aIds[] = $arr['goodid'];
            }
            $wh['id']  = array('in',$aIds);
            $field     = "id,title,price,tuan_price,qg_price,ms_price,brand,fengmian";
            $favorlist = M('Document')->where($wh)->field($field)->select();
        }

        $this->assign('favorlist', $favorlist);
        $this->assign('page',  $show);
        $this->meta_title = '我的收藏';
        $this->display();
    }
    public  function coupon() {
        $uid = $this->login();
        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        /* 优惠券调用*/
        $coupon=M("usercoupon")->where("uid='$uid'")->field('couponid,status')->select();
        $field    = "id,title,code,icon,price";
        $fcoupon=M("fcoupon")->where("display='1' and status='1'")->field($field)->limit(4)->select();

        $ids = $yhj_data =array();
        foreach($coupon as $row)
        {
            $ids[] = $row['couponid'];
        }
        if(!empty($ids))
        {
            $wh['id'] = array('in',$ids);
            $field    = "id,title,code,icon,price";
            $yhj_data = M('Fcoupon')->where($wh)->field($field)->select();
        }
        $this->assign('couponlist', $coupon);
        $this->assign('yhj_data', $yhj_data);
        $this->assign('fcouponlist', $fcoupon);
        $this->meta_title = '我的优惠券';$this->display();
    }

    /*****个人资料***************/
    public  function information() {
        $uid = $this->login();
       /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $member = D("ucenter_member");
        $field   = 'id,sex,qq,birthday,nickname,face,username,email,last_login_time,last_login_ip,mobile';
        $ucenter = $member->where("id='$uid'")->field($field)->find();
        $uface   = $ucenter['face'];

        $this->meta_title = $ucenter['username'].'个人中心';

        $is_history = '';
        $history = R('Article/view_recent');
        if(!empty($history))  $is_history = 1;

        $this->assign('information', $ucenter);
        $this->assign('username', $ucenter['username']);
        $this->assign('uface', $uface);
        $this->assign('history', $history);
        $this->assign('is_history', $is_history);
        $this->display();
    }


    public  function comment() {   /* 购物车调用*/
        $uid = $this->login();
        /** 购物车调用**/
        $cart  = $_SESSION['cart'];
        $this->assign('usercart',$cart);

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        $comment=D("Comment");
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
        $uid    = $this->login();
        $face   = $_POST['user_face'];
        if(empty($face)){
            unset($_POST['user_face']);
        }

        $info   = $_POST;
        $member = M("ucenter_member");
        $result = $member->where("id='$uid'")->save($info);
        $this->ajaxSuccess('您已成功修改！');
    }

    public  function address() {
        $uid = $this->login();

        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        if(IS_POST){

        }else{
            $address = M("transport");
            $list    = $address->where("uid='$uid'")->select();
            $user    = getUserCache();
            $this->assign('list', $list);
            $this->meta_title = $user['username'].'的地址管理';

            $this->display();
        }

    }
    //设置地址为默认
    public  function shezhi() {
        $uid = $this->login();
        if(IS_AJAX){
            $Transport = M("transport"); // 实例化transport对象
            $data['status'] = 0;
            $Transport->where("uid='$uid'")->save($data);
            $id=$_POST["id"];
            $result=$Transport->where("uid='$uid' and id='$id'")->setField("status",1);
            if($result){
                $this->ajaxSuccess('您已成功设置！');
            }else{
                $this->ajaxError('对不起，设置失败！');
            }
        }else{
            $this->error('对不起，访问有误！');
        }
    }

// 增加地址
    public  function save() {
        $uid = $this->login();
        $Transport = M("transport"); // 实例化transport对象
        $data['address'] = $_POST["posi"];
        $data['cellphone'] = $_POST["pho"];
        $data['realname'] = $_POST["rel"];
        $data['uid'] = $uid;
        $data['status'] = 0;
        $data['time']=NOW_TIME;

        if($id = $Transport->add($data)){
            $this->ajaxreturn($id);
        }else{
            $this->ajaxreturn($data);
        }
    }

   // 删除地址
    public  function deleteAddress() {
        $uid = $this->login();
        $Transport = M("transport"); // 实例化transport对象
        $id  = $_POST["id"];
        if($Transport->where("uid='$uid' and id='$id'")->delete()){
            $this->ajaxSuccess('您已成功删除！');
        }else{
            $this->ajaxError('对不起，删除失败！');
        }
    }

    //获取密码等级
    public function ajaxMimaLeve()
    {
        $uid = $this->login();
        $condition['uid'] = $uid;
        $verification = M("verification");
        $verInfo = $verification->where($condition)->field('id,email,mobile')->find();
        $num1 = $num2 = $num3 = 0;
        if(empty($verInfo)){
            if(!empty($verInfo['email']))  $num1 = 1;
            if(!empty($verInfo['mobile'])) $num2 = 1;
        }
        //支付密码设置判断
        $str  = D("ucenter_member")->where("id='$uid'")->getField('paykey');
        $code = encrypt($str,'D',''); //解密

        if($code){$num3=1;}
        $num = $num1+$num2+$num3;
        $this->ajaxSuccess(array('leve'=>$num));
    }

    /**
     * 浏览历史
     * @return mixed|string
     */
    public function history()
    {
        //访客ip
        $ip = getip();
        ////根据ip获取会员最近浏览商品，tag=3
        $count = M('Records')->where("gid > 0 and ip='$ip'")->count();
        $count = min($count,20);
        $data  = '';
        if($count > 0){
            $Page  = new \Think\Page($count,15);
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','尾页');
            $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $show = $Page->show();
            $this->assign('page',$show);

            $field = "gid,id";
            $list  = M('records')->where("gid > 0 and ip='$ip'")->limit($Page->firstRow.','.$Page->listRows)->order("time desc")->field($field)->select();

            $aIds = array();
            foreach($list as $arr)
            {
                if(!in_array($arr['gid'],$aIds))
                    $aIds[] = $arr['gid'];
            }
            $wh['id'] = array('in',$aIds);
            $field    = "id,title,price,tuan_price,qg_price,ms_price,fengmian,brand";
            $data     = M('Document')->where($wh)->field($field)->select();

        }

        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 根据订单tag获取shoplist物品以及对应详细document
     * @param $orderlist  order中的数组数据
     * @return mixed
     */
    public function shoplistAndDocumentByTag($orderlist){
        //获取shoplist的物品
        //获取document中具体物品信息
        $document = M('document');
        $detail   = M("shoplist");
        foreach($orderlist as $key=> $arr)
        {
            $shoplist = $detail->field('id,goodid,num,orderid,uid,status,create_time,price,total,sort,tag,parameters')
                              ->where("orderid={$arr['id']}")->select();

            $goodids  = $goodWhere = array();
            foreach($shoplist as $arr)
            {
                $goodids[] = $arr['goodid'];
            }
            $goodWhere['id']    = array('in',$goodids);
            $goodslist          = $document->field('title,fengmian,brand')->where($goodWhere)->select();

            if(count($goodids) == 1)
            {
                $orderlist[$key]['shoplist'][] =  array_merge($shoplist[0],$goodslist[0]);
            }else{
                for($j=0;$j<count($goodids);$j++)
                {
                    $orderlist[$key]['shoplist'][] =  array_merge($shoplist[$j],$goodslist[$j]);
                }
            }

        }

        return $orderlist;
    }
}

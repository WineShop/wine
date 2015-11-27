<?php
// +----------------------------------------------------------------------
// | yershop [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;

class CommentController extends HomeController {

    public function index() {
        if($uid = is_login()){
            $listid   = I('get.id');
            $shoplist = M('shoplist');
            $list     = $shoplist->where("uid={$uid}")->field('id,iscomment,goodid,tag')->find($listid);
            //没有评论过
            if($list["iscomment"]==1)
            {
                /** 热词调用 热门搜索**/
                $hotsearch = C('HOT_SEARCH');
                $this->assign('hotsearch',$hotsearch);

                $documen = M('document')->field('id,title,brand,fengmian')->getbyId($list["goodid"]);

                $title   = $documen['title'];
                $this->meta_title = '评价商品_'.$title;
                $this->assign('comment', $list);
                $this->assign('documen',$documen);
                $this->display();
    
            }else if($list["iscomment"]==2){
                $this->error('商品已经评价过');
            }else{
                $this->error('您未购买过该商品');
            }
        }else{
            $this->error('对不起，您还没登录！');
        }
    }


    public function add() {
      if (IS_POST) {
            $shopid = I('post.shopid');
            $uid    = is_login();
            $comment = D("comment");
            $comment -> startTrans();
            $comment->create();
            $comment->uid = $uid;
            $comment->create_time = NOW_TIME; // 增加time属性
            $comment->status = 1; // 可见

            $res1 = $comment->add();
            $res2 = M('shoplist')->where("uid={$uid} and id='$shopid'")->setField("iscomment","2");

            if($res1 && $res2)
            {
                $comment->commit();
                $this->ajaxSuccess('您已成功评论！');
            }else{
                $comment->rollback();
                $this->ajaxError('对不起，评论失败了！');
            }

        }else {
            $this->error('对不起，非法访问！');
        }
       
    }


}

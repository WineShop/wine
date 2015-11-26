<?php
// +----------------------------------------------------------------------
// | yershop [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;

class CommentController extends Controller {

    public function index() {
        if(is_login()){
            $listid=I('get.id');
            $shoplist=M('shoplist');
            $list= $shoplist->find($listid);
            //没有评论过
            if($list["iscomment"]==1)
            {
                 /* 热词调用*/
                $hotsearch=R("Index/getHotsearch");
                $this->assign('hotsearch',$hotsearch);

                /** * 控制器必须！**/

                $this->assign('comment', $list);
                $title=get_good_name($list["goodid"]);
                $this->meta_title = '评价商品_'.$title;
                $this->display();
    
            }else {
                $this->error('商品评价过');
            }
        }else{
            $this->redirect('User/login');
        }
    }


    public function add() {
    
      if (IS_POST) {
            $uid = is_login();
            $comment=D("comment");
            $comment->create();
            $comment->uid = $uid; // 修改数据对象的status属性
            $comment->create_time = NOW_TIME; // 增加time属性
            $comment->status = 1; // 增加time属性
            $id=$comment->add();
            if($id>0){
                $this->success('商品评价成功',U('Center/index'));
                M('shoplist')->where("id='$shopid'")->setField("iscomment","2");
            }
        }else {
            $this->redirect('商品评价失败');
        }
       
    }


}

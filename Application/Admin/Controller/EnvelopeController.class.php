<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 lamp365@163.com All rights reserved.
// +----------------------------------------------------------------------
// | author kevin <lamp365@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台订单控制器
  * @author kevin <lamp365@163.com>
 */
class EnvelopeController extends AdminController {

    /**
     * 订单管理
     * author kevin <lamp365@163.com>
     */
    public function index(){
        /* 查询条件初始化 */
	
        $map  = '';
        $title=trim(I('get.title'));
        $field  = 'id,username,uid,sendname,title,status,view,create_time,update_time,group';

        if($title){
            $map['content'] = array('like',"%{$title}%");
            $list   =   M("Envelope")->where($map)->field(true)->order('id desc')->field($field)->select();
        }else{
            $list = $this->lists('Envelope', $map,'id desc',$field);
        }

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '站内信管理';
        $this->display();
    }

  /* 编辑分类 */
    public function edit($id = null, $pid = 0){
       
	    $Envelope = D('Envelope');    
        if(IS_POST){ //提交表单
            if(	false !== $Envelope->update()){
			
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $Envelope->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = '';
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Envelope->info($pid, 'id,name,title,status');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级站内信不存在或被禁用！');
                }
            }

            /* 获取分类信息 */
            $info = $id ? $Envelope->info($id) : '';        
            $this->assign('info',$info);

            $this->meta_title = '编辑站内信';
            $this->display();
        }
    }

    /* 新增分类 */
    public function add(){
        $Envelope = D('Envelope');
	 
        if(IS_POST){ //提交表单
	      
			if(false !== $Envelope->update()){
                $personenvelope=M('personenvelope');
                $personenvelope->create();
                $personenvelope->title=$_POST['title'];
                $personenvelope->content=$_POST['content'];
                $personenvelope->group=$_POST['group'];
                $personenvelope->uid=$_POST['uid'];
                $personenvelope->status=1;
                $personenvelope->create_time=NOW_TIME;
                $personenvelope->add();
                $this->success('新增成功！', U('index'));
            } else {
                $error = $Envelope->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {

            $this->meta_title = '新增站内信';
            $this->display("edit");
        }
    }


 public function del(){
       if(IS_POST){
             $ids = I('post.id');
            $order = M("Envelope");
			
            if(is_array($ids)){
                foreach($ids as $id){
                     $order->where("id='$id'")->delete();
                }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("Envelope");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }

}
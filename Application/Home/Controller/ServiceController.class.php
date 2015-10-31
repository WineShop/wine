<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ServiceController extends HomeController {
    /* 频道封面页 */
	public function index(){
        /** 分类信息id **/
        $id= I('get.id');
        if(empty($id))
            $this->error('对不起，参数有误！');

        /*栏目页统计代码实现，tag=2*/
         if(1==C('IP_TONGJI')){
           $record=IpLookup("",2,'server');
         }
        /** 热词调用 热门搜索**/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);

        //分类一维数组
        $category=M("category")->where("id='$id'")->field('id,name,pid,title')->find();
        //获取最大的文章id
        $info=M("document")->where("category_id='$id'and model_id='2'")->order("id desc")->field('id')->limit(1)->find();

       /**获取文章明细**/
       if(!empty($info)){
             $data = D('Document')->detail($info['id']);
       }
        /**设置网站标题，一维数组**/
       $pid       = $category['pid'];
       $pcategory = M("category")->where("id='$pid'")->field('title')->find();

       $this->meta_title = $category['title']."-".$pcategory['title'];
       $position="<p class='red fwb'>".$pcategory['title']."</p>><p class='red fwb'>".$category['title']."</p>";
       $this->assign('position',$position);
        $this->assign('info',$data);

        if(empty(S(C('HOME_SERVER_MENU'))))
        {
            $serverMenuList=$this->AllMenu();
            S(C('HOME_SERVER_MENU'),$serverMenuList,3600*24*30);   //缓存一个月

        }else{
            $serverMenuList = S(C('HOME_SERVER_MENU'));
        }
        $this->assign('serverMenuList',$serverMenuList);
		$this->display();
	}

    /**
     * 获取售后服务列表
     * @param  integer  $category 分类ID
     * @param  string   $order    排序规则
     * @param  integer  $status   状态
     * @param  boolean  $count    是否返回总数
     * @param  string   $field    字段 true-所有字段
     * @return array              文档列表
     */
    public function AllMenu(){
        /* 一级分类信息 */
        $field   = "id,name,title,pid,sort,model,link_id,display,status,ismenu";
        $menu    = M("category")->where("ismenu='2' and pid='0'")->field($field)->order("id asc")->select();
        $sonmenu = M("category");
        foreach($menu as $n=> $val)
        {
            $menu[$n]['id']=$sonmenu->where('pid=\''.$val['id'].'\'')->field($field)->select();

        }
        return $menu;
    }


    public function add(){
        $data=M("member")->limit(1)->find();
        $this->assign('info',$data);
        $this->display();
    }

    public function addmessage(){
        if(IS_POST){
            $message=M("message");
            $message->create();
            $data["content"]  = $_POST["content"];
            $data["goodid"]   = $_POST["goodid"];
            $data["uid"]      = D('member')->uid();
            $data["create_time"]=NOW_TIME;
            $data["time"]     = date("Y-m-d H:i:s",time());
            $data["status"]   = 1;
            if($message->add($data))
            {
                $this->ajaxSuccess($data);
            }

        }
    }

}

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
class SearchController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
        $keyword = I('words');//获取分类的英文名称
        $key     = I('get.order');
        $sort    = I('get.sort');
       if(isset($key)){
            if($key=="1"){ $listsort="view"." ".$sort;}
            if($key=="2"){ $listsort="id"." ".$sort;}
            if($key=="3"){  $listsort="price"." ".$sort;}
            if($key=="4"){  $listsort="sale"." ".$sort;}
       }
        if(empty($key)){$key="1";
            $see="asc";
            $order="view";
            $sort="asc";
            $listsort=$order." ".$sort;
        }

         if($sort=="asc"){$see="desc";}
         if($sort=="desc"){$see="asc";}
          $this->assign('see',$see);
          $this->assign('order',$key);
          $this->assign('value',$sort);

          $map['title|name|description']  = array('like','%'.$keyword.'%');
          $count=M('Document')->where($map)->count();
          $Page= new \Think\Page($count,12);
          $Page->setConfig('prev','上一页');
          $Page->setConfig('next','下一页');
          $Page->setConfig('first','第一页');
          $Page->setConfig('last','尾页');
          $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
          $show= $Page->show();

          $field     = "id,title,price,tuan_price,qg_price,ms_price,brand,fengmian";
          $list=M('Document')->where($map)->field($field)->order($listsort)->limit($Page->firstRow.','.$Page->listRows)->select();
          $this->assign('list',$list);// 赋值数据集
          $this->assign('page',$show);


          $this->assign('searchlist', $list);
          $this->assign('keyword', $keyword);

        /* 热词调用*/
        $hotsearch = C('HOT_SEARCH');
        $this->assign('hotsearch',$hotsearch);
        $this->meta_title = $keyword.'的搜索结果';
        $this->display();
    }

}

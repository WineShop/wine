<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;
use Think\Page;

/**
 * 文档基础模型
 */
class FavortableModel extends Model{
   
    public  function getfavor() {
        $uid       = is_login();
        $order     = D("favortable");
        $favorlist = $order->where("uid='$uid'")->field('id,goodid,create_time,num')->select();
        return $favorlist;
    }

}

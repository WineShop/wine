<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 15-11-25
 * Time: 上午12:22
 */
namespace Admin\Controller;
class OrdertopayController extends AdminController {

    public function index(){
        /* 查询条件初始化 */

        $map    = array('status' => -1);
        $field  = 'id,tag,orderid,pricetotal,create_time,status,uid,display,ispay,total,addressid,message,backinfo';
        $list   = $this->lists('order', $map,'id desc',$field);

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '等待支付订单管理';
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

}
<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Upload\Driver\Qiniu\QiniuStorage;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {

    public function _initialize(){
        $config = array(
            'accessKey'  => C('ACCESS_KEY'),
            'secrectKey' => C('SECRET_KEY'),
            'bucket'     => C('BUCKET'),
            'domain'     => C('QINIUDOMAIN'),
        );
        $this->qiniu = new QiniuStorage($config);
        parent:: _initialize();
    }


    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

        /* 记录附件信息 */
        if($info){
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }

    /**
     * 将图片上传到七牛
     */
    public function uploadPictureQiniu()
    {
        $tmp_file = $_FILES['qiniu_file'];

        //文件不能过大大于2造
        if($this->checkImgSize($tmp_file['size'])){
            $this->ajaxReturn(array(
                'is_data'    => 'no',
                'error_code' => '1002',
                'errorStr'   => '对不起，文件不能大于2M'
            ));
            exit;
        }

        $file = array(
            'name'     => 'file',
            'fileName' => $this->setImgName($tmp_file['name']),
            'fileBody' => file_get_contents($tmp_file['tmp_name'])
        );

        $config = array();
        $result = $this->qiniu->upload($config, $file);

        if($result){
           /* (
                        [hash] => FkVJw_PXfSZihFMMW2gWzhh9nAsT
                        [key] => 2015-11-01 23:09:52 的屏幕截图.png
            )*/
            $result['is_data'] = 'yes';
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn(array(
                'is_data'    => 'no',
                'error_code' =>$this->qiniu->error,
                'errorStr'   =>$this->qiniu->errorStr
            ));
        }
        exit;
    }


    public function editUploadQiniu()
    {
        $tmp_file = $_FILES['imgFile'];
        if(empty($tmp_file)){
            $return = array('error'=>1,'message'=>'对不起，你没有上传任何图片');
            exit(json_encode($return));
        }

        //文件不能过大大于2造
        if($this->checkImgSize($tmp_file['size'])){
            $return = array('error'=>1,'message'=>'对不起，文件不能大于2M');
            exit(json_encode($return));
        }

        $file = array(
            'name'    => 'file',
            'fileName'=> $this->setImgName($tmp_file['name']),
            'fileBody'=> file_get_contents($tmp_file['tmp_name'])
        );

        $config = array();
        $result = $this->qiniu->upload($config, $file);

        if($result){
            /* (
                         [hash] => FkVJw_PXfSZihFMMW2gWzhh9nAsT
                         [key] => 2015-11-01 23:09:52 的屏幕截图.png
             )*/
            $url = C("QINIUDOMAIN").'/'.$result['key'];
            $return = array('error'=>0,'url'=>$url);
            exit(json_encode($return));
        }else{
            $return = array('error'=>1,'message'=>$this->qiniu->errorStr);
            exit(json_encode($return));
        }

    }

    /**
     * 设置文件名
     * @param $img
     * @return string
     */
    public function setImgName($img)
    {
        $imgArr     = explode('.',$img);
        $houzui     = array_pop($imgArr);
        $fileName   = date('YmdHis',time()).rand(100,999).uniqid().'.'.$houzui;
        return $fileName;
    }

    public function checkImgSize($size)
    {
        $total = pow(1024,2)*2;
        if($size > $total){
           return true;
        }
        return false;
    }

    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture(){
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
}

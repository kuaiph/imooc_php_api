<?php

/**
 * @name ArtController
 * @author joe
 * @desc 文章控制器
 */
class ArtController extends Yaf_Controller_Abstract {


    public function indexAction(){
        return $this->listAction();
    }


    public function addAction($artId=0) {
        if( !$this->_isAdmin() ) {
            echo Common_Request::response(-2000);
            return false;
        }
        $submit = Common_Request::getRequest("submit","0");
        if( $submit != "1" ) {
            echo Common_Request::response(-2001);
            return false;
        }

        $title     = Common_Request::postRequest("title",false);
        $contents  = Common_Request::postRequest("contents",false);
        $author    = Common_Request::postRequest("author",false);
        $cate      = Common_Request::postRequest("cate",false);

        if( !$title || !$contents || !$author || !$cate ) {
            echo Common_Request::response(-2002);
            return false;
        }

        $model = new ArtModel();
        if( $lastId = $model->add( trim($title), trim($contents), trim($author), trim($cate), $artId)) {
            echo Common_Request::response(0,array("lastId"=>$lastId));
            return false;
        } else {
            echo Common_Request::response($model->errno);
            return false;
        }

    }


    public function editAction() {
        if( !$this->_isAdmin() ){
            echo Common_Request::response(-2000);
            return false;
        }
        $artId = Common_Request::getRequest("artId","0");
        if( is_numeric($artId) && $artId ) {
            return $this->addAction($artId);
        } else {
            echo Common_Request::response(-2003,"缺少必要的参数");
            return false;
        }

    }

    /**
     * 删除文章
     * @return bool
     */
    public function delAction() {
        if( !$this->_isAdmin() ){
            echo Common_Request::response(-2000,"需要管理员权限");
            return false;
        }
        $artId = Common_Request::getRequest("artId","0");
        if( is_numeric($artId) && $artId ) {
            $model = new ArtModel();
            if( $model->del( $artId ) ) {
                echo Common_Request::response();
            } else {
                echo Common_Request::response($model->errno);
            }
        } else {
            echo Common_Request::response(-2003);
        }
        return false;
    }

    /**
     * 更改文章状态
     * @return bool
     */
    public function statusAction() {
        if( !$this->_isAdmin() ) {
            echo Common_Request::response(-2000);
            return false;
        }
        $artId  = Common_Request::getRequest("artId","0");
        $status = Common_Request::getRequest("status","offline");
        if( is_numeric($artId)  && $artId ) {
            $model = new ArtModel();
            if( $model->status($artId, $status) ){
                echo Common_Request::response();
            } else {
                echo Common_Request::response($model->errno);
            }
        } else {
            echo Common_Request::response(-2003);
        }
        return false;
    }

    /**
     * 获取文章信息
     * @return bool
     */
    public function getAction() {
        $artId = Common_Request::getRequest("artId","0");
        if( is_numeric($artId)  && $artId ) {
            $model = new ArtModel();
            if( $data = $model->get($artId) ) {
                echo Common_Request::response(0,$data);
            } else {
                echo Common_Request::response($model->errno);

            }
        } else {
            echo Common_Request::response(-2007);
        }
        return false;
    }


    public function listAction() {
        $pageNo   = Common_Request::getRequest("pageNo","0");
        $pageSize = Common_Request::getRequest("pageSize","10");
        $cate     = Common_Request::getRequest("cate","0");
        $status   = Common_Request::getRequest("status","online");

        $model = new ArtModel();
        if( $data = $model->listArt($pageNo, $pageSize, $cate, $status) ){
            echo Common_Request::response(0,$data);
        } else {
            echo Common_Request::response($model->errno);
        }
        return false;
    }


    private function _isAdmin() {
        return true;
    }
}

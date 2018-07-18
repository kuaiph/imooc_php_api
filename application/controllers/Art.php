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
            echo json_encode(
                array(
                    "errno"  => -2000,
                    "errmsg" => "需要管理员权限",
            ));
            return false;
        }

        $submit = $this->getRequest()->getQuery("submit","0");
        if( $submit != "1" ) {
            echo json_encode(
                array(
                    "errno"  => -2001,
                    "errmsg" => "请通过正常渠道提交",
                ));
            return false;
        }

        $title     = $this->getRequest()->getPost("title",false);
        $contents  = $this->getRequest()->getPost("contents",false);
        $author    = $this->getRequest()->getPost("author",false);
        $cate      = $this->getRequest()->getPost("cate",false);

        if( !$title || !$contents || !$author || !$cate ) {
            echo json_encode(
                array(
                    "errno"  => -2002,
                    "errmsg" => "没填写完整",
            ));
            return false;
        }

        $model = new ArtModel();
        if( $lastId = $model->add( trim($title), trim($contents), trim($author), trim($cate), $artId)) {
            echo json_encode(
                array(
                    "errno"  => 0,
                    "errmsg" => '',
                    "data"   => array("lastId"=>$lastId),
                ));
            return false;
        } else {
            echo json_encode(
                array(
                    "errno"  => $model->errno,
                    "errmsg" => $model->errmsg,
                ));
            return false;
        }

    }


    public function editAction() {
        if( !$this->_isAdmin() ){
            echo json_encode(array(
                "errno"  => -2000,
                "errmsg" => "需要管理员权限",
            ));
            return false;
        }
        $artId = $this->getRequest()->getQuery("artId","0");
        if( is_numeric($artId) && $artId ) {
            return $this->addAction($artId);
        } else {
            echo json_encode(
                array(
                    "errno"  => -2003,
                    "errmsg" => "缺少必要的参数",
                ));
            return false;
        }

    }

    /**
     * 删除文章
     * @return bool
     */
    public function delAction() {
        if( !$this->_isAdmin() ){
            echo json_encode(
                array(
                    "errno"  => -2000,
                    "errmsg" => "需要管理员权限",
                ));
            return false;
        }
        $artId = $this->getRequest()->getQuery("artId","0");
        if( is_numeric($artId) && $artId ) {
            $model = new ArtModel();
            if( $model->del( $artId ) ) {
                echo json_encode(
                    array(
                        "errno"  => 0,
                        "errmsg" => "",
                    ));
            } else {
                echo json_encode(
                    array(
                        "errno"  => $model->errno,
                        "errmsg" => $model->errmsg,
                    ));
            }
        } else {
            echo json_encode(
                array(
                    "errno"  => -2003,
                    "errmsg" => "缺少必要的参数",
                ));
        }
        return false;
    }

    /**
     * 更改文章状态
     * @return bool
     */
    public function statusAction() {
        if( !$this->_isAdmin() ) {
            echo json_encode(
                array(
                    "errno"  => -2000,
                    "errmsg" => "需要管理员权限",
                ));
            return false;
        }
        $artId  = $this->getRequest()->getQuery("artId","0");
        $status = $this->getRequest()->getQuery("status","offline");
        if( is_numeric($artId)  && $artId ) {
            $model = new ArtModel();
            if( $model->status($artId, $status) ){
                echo json_encode(
                    array(
                        "errno"  => 0,
                        "errmsg" => "",
                    ));
            } else {
                echo json_encode(
                    array(
                        "errno"  => $model->errno,
                        "errmsg" => $model->errmsg,
                    ));
            }
        } else {
            echo json_encode(
                array(
                    "errno"  => -2003,
                    "errmsg" => "缺少必要的参数",
                ));
        }
        return false;
    }

    /**
     * 获取文章信息
     * @return bool
     */
    public function getAction() {
        $artId = $this->getRequest()->getQuery("artId","0");
        if( is_numeric($artId)  && $artId ) {
            $model = new ArtModel();
            if( $data = $model->get($artId) ) {
                echo json_encode(
                    array(
                        "errno"  => 0,
                        "errmsg" => "",
                        "data"   => $data,
                    ));
            } else {
                echo json_encode(
                    array(
                        "errno"  => $model->errno,
                        "errmsg" => $model->errmsg,
                    ));

            }
        } else {
            echo json_encode(
                array(
                    "errno"  => -2007,
                    "errmsg" => "缺少必要的ID参数",
                ));
        }
        return false;
    }


    public function listAction() {
        $pageNo   = $this->getRequest()->getQuery("pageNo","0");
        $pageSize = $this->getRequest()->getQuery("pageSize","10");
        $cate     = $this->getRequest()->getQuery("cate","0");
        $status   = $this->getRequest()->getQuery("status","online");

        $model = new ArtModel();
        if( $data = $model->list($pageNo, $pageSize, $cate, $status) ){
            echo json_encode(
                array(
                    "errno" => 0,
                    "errmsg"=> "",
                    "data"  => $data,
                ));
        } else {
            echo json_encode(
                array(
                    "errno" => $model->errno,
                    "errmsg"=> $model->errmsg,
                ));
        }
        return false;
    }


    private function _isAdmin() {
        return true;
    }
}
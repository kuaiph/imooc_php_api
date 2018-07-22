<?php
/**
 * @name ArtModel
 * @desc 文章操作model类
 * @author joe
 */

class ArtModel {
    public $errno  = 0;
    public $errmsg = '';
    private $_dao   = null;

    public function __construct() {
        $this->_dao = new Db_Art();
        //防止pdo在拼接sql的时候将int转string
    }

    /**
     * 文章添加model
     * @param $title
     * @param $contents
     * @param $author
     * @param $cate
     * @param int $artId
     * @return bool|int
     */
    public function add($title, $contents, $author, $cate, $artId=0) {
        $isEdit = false;
        if( $artId != 0 && is_numeric($artId)) {
            if(!$this->_dao->findArt($artId=0)){
                $this->errno = $this->_dao->errno();
                $this->errmsg = $this->_dao->errmsg();
                return false;
            }
            $isEdit = true;
        } else {
            //检测分类是否存在
            if(!$this->_dao->findCate($cate)){
                $this->errno = $this->_dao->errno();
                $this->errmsg = $this->_dao->errmsg();
                return false;
            }
        }

        /**
         * 插入或更新文章内容
         */

        $data = array($title,$contents,$author,intval($cate));

        if(!($lastid = $this->_dao->modify($data,$isEdit,$artId))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }else{
            /**
             * 返回文章最后的id值
             */
            return $lastid;
        }

    }

    /**
     * 删除数据
     * @param $artId
     * @return bool
     */
    public function del($artId) {
        if(!$this->_dao->del($artId)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        return true;
    }

    /**
     * 更改状态
     * @param $artId
     * @param string $status
     * @return bool
     */
    public function status($artId, $status="offline") {
        if(!$this->_dao->status($artId,$status)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        return true;
    }

    /**
     * 文章信息获取
     * @param $artId
     * @return array|bool
     */
    public function get($artId) {
        if(!($artInfo = $this->_dao->getArt($artId))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        $data = array(
            'id'       =>  intval($artId),
            'title'    =>  $artInfo['title'],
            'contents' =>  $artInfo['contents'],
            'author'   =>  $artInfo['author'],
            'cateName' =>  $artInfo['cateName'],
            'cateId'   =>  intval($artInfo['cate']),
            'ctime'    =>  $artInfo['ctime'],
            'mtime'    =>  $artInfo['mtime'],
            'status'   =>  $artInfo['status'],
        );
        return $data;
    }

    /**
     * 文章分类信息获取-分页
     * @param int $pageNo
     * @param int $pageSize
     * @param int $cate
     * @param string $status
     * @return array|bool
     */
    public function listArt($pageNo=0, $pageSize=10, $cate=1, $status="online") {
        $start = $pageNo * $pageSize +($pageNo==0?0:1);
        if(!$ret = $this->_dao->getListArt($start,$status,$pageSize,$cate=1)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        $data      = array();
        $cateInfo  = array();
        foreach( $ret as $item) {
            /**
             * 获取分类信息
             */

            if( isset($cateInfo[$item['cate']]) ){
                $cateName = $cateInfo[$item['cate']];
            } else {

                if(!($retCate = $this->_dao->findCate($item['cate']))){
                    $this->errno = $this->_dao->errno();
                    $this->errmsg = $this->_dao->errmsg();
                    return false;
                }
                $cateName = $cateInfo[$item['cate']] = $retCate[0]['name'];
            }

            /**
             * 切割文章
             */

            $contents = mb_strlen($item['contents'])>30?mb_substr($item['contents'],0,30)."...":$item['contents'];
            $data[] = array(
                'id'       =>  intval($item['id']),
                'title'    =>  $item['title'],
                'contents' =>  $contents,
                'author'   =>  $item['author'],
                'cateName' =>  $cateName,
                'cateId'   =>  intval($item['cate']),
                'ctime'    =>  $item['ctime'],
                'mtime'    =>  $item['mtime'],
                'status'   =>  $item['status'],
            );
        }
        return $data;
    }
}

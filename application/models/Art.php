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
    }

    /**
     * 文章添加model
     * @param string $title    文章标题
     * @param string $contents 文章内容
     * @param string $author   作者
     * @param int    $cate     分类
     * @param int    $artId    文章ID
     * @return mixed
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
     * 根据文章ID删除文章
     * @param string $artId 文章ID
     * @return bool 删除成功与否
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
     * @param int $artId 文章ID
     * @param string $status 文章状态
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
     * 通过文章id获取文章信息
     * @param int $artId 文章ID
     * @return mixed
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
     * @param int $pageNo      第几页
     * @param int $pageSize    分页大小
     * @param int $cate        分类
     * @param string $status   文章状态
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

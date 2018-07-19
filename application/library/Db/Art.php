<?php
class Db_Art extends Db_Base {

    public function findArt($artId=0){
            $query = self::getDb()->prepare("select count(*) from `art` where `id` = ?");
            $query->execute(array($artId));
            $ret = $query->fetchAll();
            if(!$ret||count($ret)!=1) {
                self::$errno = -2004;
                self::$errmsg= "找不到文章,请确认是否有该文章";
                return false;
            }
        return true;
    }

    public function findCate($cate){
        $query = self::getDb()->prepare("select count(*) from `cate` where `id` = ?");
        $query->execute(array($cate));
        $ret = $query->fetchAll();
        if(!$ret||$ret[0][0]==0) {
            self::$errno = -2005;
            self::$errmsg= "找不到 ".$cate."分类信息";
            return false;
        }
        return $ret;
    }

    public function modify($data,$isEdit,$artId){
        if( !$isEdit ){
            $query = self::getDb()->prepare("insert into `art` (`title`,`contents`,`author`,`cate`) VALUES(?,?,?,?)");

        } else {
            $query = self::getDb()->prepare("update `art` set `title`=?,`contents`=?,`author`=?,`cate`=? where `id`=? ");
            $data[] = $artId;
        }
        $ret = $query->execute($data);
        if(!$ret) {
            self::$errno  = -2006;
            self::$errmsg = "操作文章数据表失败,errinfo:".end($query->errorInfo());
            return false;
        }
        if(!$isEdit){
            return intval(self::getDb()->lastInsertId());
        } else {
            return intval($artId);
        }
    }

    public function del($artId){
        $query = self::getDb()->prepare("delete from `art` where `id`=?");
        $ret = $query->execute(array($artId));
        if( !$ret ) {
            self::$errno  = -2007;
            self::$errmsg = "删除数据失败".end($query->errorInfo());
            return false;
        }
        return true;
    }

    public function status($artId,$status="offline"){
        $query = self::getDb()->prepare("update `art` set `status` = ? where `id` = ?" );
        $ret   = $query->execute(array($status,intval($artId)));
        if( !$ret ) {
            self::$errno  = -2008;
            self::$errmsg = "更新文章状态失败".end($query->errorInfo());
            return false;
        }
        return true;
    }

    public function getArt($artId){
        $query  = self::getDb()->prepare("select `title`, `contents`, `author`, `cate`,`ctime`,`mtime`,`status` from `art` where `id` = ?");
        $status = $query->execute(array(intval($artId)));
        $ret    = $query->fetchAll();
        if( !$status || !$ret ){
            self::$errno  = -2009;
            self::$errmsg = "查询失败".end($query->errorInfo());
            return false;
        }
        $artInfo = $ret[0];

        /**
         * 获取分类信息
         */
        $query = self::getDb()->prepare("select `name` from `cate` where `id`=?");
        $query->execute(array($artInfo['cate']));
        $ret   = $query->fetchAll();
        if( !$ret ) {
            self::$errno  = -2010;
            self::$errmsg = "获取分类信息失败".end($query->errorInfo());
            return false;
        }
        $artInfo['cateName'] = $ret['0']['name'];
        return $artInfo;
    }

    public function getListArt($start,$status,$pageSize,$cate=1){
        if($cate == 0){
            $filter = array($status, intval($start),intval($pageSize));
            $query = self::getDb()->prepare("select `id`, `title`, `contents`, `author`, `cate`, `ctime`, `mtime`,
 `status` from `art` where `status`= ? order by `ctime` desc limit ?, ?");
        }else{
            $filter = array($cate, $status, intval($start), intval($pageSize) );
            $query = self::getDb()->prepare("select `id`, `title`, `contents`, `author`, `cate`,`ctime`, `mtime`,
            `status` from `art` where `cate` = ? and `status` = ? order by `ctime` desc limit ?,?");
        }
        //$query->bindValue('limit', (int) $start, PDO::PARAM_INT);
        $stat = $query->execute($filter);
        $ret  = $query->fetchAll();
        if( !$ret ){
            self::$errno  = -2011;
            self::$errmsg = "获取文章列表失败, errinfo";
            return false;
        }
        return $ret;
    }

}
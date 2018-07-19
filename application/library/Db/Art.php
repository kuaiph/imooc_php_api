<?php
class Db_Art extends Db_Base {

    public function findArt($artId=0){
            $query = self::getDb()->prepare("select count(*) from `art` where `id` = ?");
            $query->execute(array($artId));
            $ret = $query->fetchAll();
            if(!$ret||count($ret)!=1) {
                list(self::$errno, self::$errmsg) = Err_Map::get(-2004);
                return false;
            }
        return true;
    }

    public function findCate($cate){
        $query = self::getDb()->prepare("select count(*) from `cate` where `id` = ?");
        $query->execute(array($cate));
        $ret = $query->fetchAll();
        if(!$ret||$ret[0][0]==0) {
            list(self::$errno,self::$errmsg) = Err_Map::get(-2005);
            self::$errmsg .= $cate;
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
            list(self::$errno, self::$errmsg) = Err_Map::get(-2006);
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
            list(self::$errno,self::$errmsg) = Err_Map::get(-2012);
            self::$errmsg .= end($query->errorInfo());
            return false;
        }
        return true;
    }

    public function status($artId,$status="offline"){
        $query = self::getDb()->prepare("update `art` set `status` = ? where `id` = ?" );
        $ret   = $query->execute(array($status,intval($artId)));
        if( !$ret ) {
            list(self::$errno,self::$errmsg) = Err_Map::get(-2008);
            self::$errmsg .= end($query->errorInfo());
            return false;
        }
        return true;
    }

    public function getArt($artId){
        $query  = self::getDb()->prepare("select `title`, `contents`, `author`, `cate`,`ctime`,`mtime`,`status` from `art` where `id` = ?");
        $status = $query->execute(array(intval($artId)));
        $ret    = $query->fetchAll();
        if( !$status || !$ret ){
            list(self::$errno,self::$errmsg) = Err_Map::get(-2009);
            self::$errmsg .= end($query->errorInfo());
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
            list(self::$errno,self::$errmsg) = Err_Map::get(-2010);
            self::$errmsg .= end($query->errorInfo());
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
            list(self::$errno,self::$errmsg) = Err_Map::get(-2011);
            return false;
        }
        return $ret;
    }

}

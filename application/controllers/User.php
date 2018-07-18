<?php
/**
 * @name IndexController
 * @author joe
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UserController extends Yaf_Controller_Abstract {


    public function indexAction(){
        return $this->loginAction();
    }


    /**
     * 登录接口
     * @return bool
     */
    public function loginAction(){
        $submit = $this->getRequest()->getQuery("submit","0");
        if( $submit != "1") {
            echo json_encode(
                array(
                    "errno"    => -1001,
                    "errmsg"   => "请通过正常渠道提交",
                ));
            return false;
        }

        //获取参数
        $uname = $this->getRequest()->getPost("uname",false);
        $pwd   = $this->getRequest()->getPost("pwd",false);
        if( !$uname || !$pwd ) {
            echo json_encode(
                array(
                    "errno"    => -1002,
                    "errmsg"   => "用户名或密码不能为空",
                ));
            return false;
        }

        //调用model，做登录验证
        $model = new UserModel();
        $uid = $model->login(trim($uname),trim($pwd));

        if( $uid ) {
            session_start();
            $_SESSION['user_token']      = md5("salt".$_SERVER['REQUEST_TIME'].$uid);
            $_SESSION['user_token_time'] = $_SERVER['REQUEST_TIME'];
            $_SESSION['user_id']         = $uid;
            echo json_encode(array(
                "errno"    => 0,
                "errmsge"  =>'',
                "data"     => array("name"=>$uname),
            ));
        } else {
            echo json_encode(array(
                "errno"    => $model->errno,
                "errmsg"   => $model->errmsg,
            ));
        }
        return false;
    }

    /**
     * 注册接口
     * @return bool
     */
	public function registerAction() {
		//1. fetch query
		$uname = $this->getRequest()->getPost("uname",false);
		$pwd   = $this->getRequest()->getPost("pwd",false);
		if( !$uname || !$pwd ) {
		    echo json_encode(
		        array(
		            "errno"    => -1002,
                    "errmsg"   => "用户名或密码不能为空",
            ));
		    return false;
        }

		//2. fetch model
		$model = new UserModel();
		if( $model->register(trim($uname),trim($pwd)) ){
		    echo json_encode(
		      array(
		          "errno"    => 0,
                  "errmsg"   => '',
                  "data"     => array("name"=>$uname)
              ));
        } else {
            echo json_encode(
                array(
                    "errno"  => $model->errno,
                    "errmsg" => $model->errmsg
            ));
        }


		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return false;
	}
}

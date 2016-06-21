<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
require(__DIR__ . '/../../vendor/Captcha_code.php');
/**
 * Login controller
 */
class LoginController extends Controller
{
/**
 * 登录 
 */
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;


	public function actionIndex(){

/**
 * 判断session_id 是否存在 if(存在){跳到主页面}else{跳到登录页面}
 */
		return $this->render('login.html');
	}
/**
 * 注册
 */
	public function actionRegister(){
		return $this->render('register.html');
	}
    /**
    * 注册成功
    */
    /**
     * 验证码
     */
	public function actionYanzhen(){
        session_start();
        //构造方法
        $code=new \ValidationCode(80, 20, 4);
        $code->showImage();   //输出到页面中供 注册或登录使用
        $_SESSION["code"]=$code->getCheckCode();  //将验证码保存到服务器中
	}

    public function actionYan_code($yan_code=""){
        if($yan_code==""){
            $yan_code = Yii::$app->request->post('yan_code');
        }
        session_start();
        if(!empty($_SESSION["code"])){
            if(strtolower($_SESSION["code"])==strtolower($yan_code)){
                echo 1;
            }else{
                echo 0;
            }
        }
        else{
            echo 0;
        }
       //将验证码保存到服务器中
    }
    /**
     * 登录ajax验证
     * return   1：用户名为空
     *          2：密码为空
     *          3：用户名类型错误
     *          4：密码类型错误
     *          5：登陆成功
     *          6：用户名或密码错误
     *          6：验证码错误
     */
    public function actionLogining()
    {
        session_start();
        //消除session
        unset($_SESSION['user']);
        unset($_SESSION['identity']);
        //接受登录值
        $arr = Yii::$app->request->post();

        //邮箱验证
        if ($arr['email'] == "") {
            $st = 1;
        }
        //密码验证
        if ($arr['password'] == "") {
            $st = 2;
        }
        //邮箱正则
        $preg_username = '/^[0-9]{5,10}$/';
        if (!preg_match($preg_username, $arr['email'])) {
            $st = 3;
        }

        $preg_pwd = '/^[0-9]{5,10}$/';
        //密码正则
        if (!preg_match($preg_pwd, $arr['password'])) {
            $st = 4;
        } else {
            if(strtolower($_SESSION["code"])==strtolower($arr['yan_code'])){
                    /**查询数据库*/
          $pwd_md5 = md5(sha1($arr['password']) . "kuaipin" . sha1($arr['email']));//加密过的
                //判断是哪个类型的用户登录
                if($arr['sel']=='person'){
                    $rows = (new \yii\db\Query())
                        ->select(['*'])
                        ->from('kp_user')
                        ->where("user_email='$arr[email]' and password='$pwd_md5'")
                        ->one();
                    if ($rows['user_number'] != "") {
                        $_SESSION['user'] = $rows['u_id'];//登录成功，把用户的u_id存入session
                        $_SESSION['identity'] = $arr['sel'];//登录成功，把用户的登陆目的存入session
                        $st = 5;//密码正确
                    } else {
                        $st = 6;//用户名或密码错误
                    }
                }elseif($arr['sel']=='company'){
                    $rows = (new \yii\db\Query())
                        ->select(['*'])
                        ->from('kp_company_register')
                        ->where("cr_email='$arr[email]' and password='$pwd_md5'")
                        ->one();
                    if ($rows) {
                        $_SESSION['user'] = $rows['cr_id'];//登录成功，把用户的u_id存入session
                        $_SESSION['identity'] = $arr['sel'];//登录成功，把用户的登陆目的存入session
                        $st = 5;//密码正确
                    } else {
                        $st = 6;//用户名或密码错误
                    }
                }
            }else{
                $st=7;
            }
        }
            echo $st;//ajax返回
    }

    //退出
    public function actionLogout(){
        session_start();
        //消除session
        unset($_SESSION['user']);
        unset($_SESSION['identity']);
        $this->redirect_message('退出成功','success',3,"index.php?r=index/index");
    }

}
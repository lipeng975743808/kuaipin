<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use app\models\kpuser;
use app\models\kpcompanyregister;
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
        //消除session
        session_start();
        unset($_SESSION['user']);
        unset($_SESSION['identity']);
        $this->redirect_message('退出成功','success',3,"index.php?r=index/index");
    }
    /**
         * 用户企业注册唯一性
         */
        function actionOne(){
        //接收数据
                $arr = Yii::$app->request->post();
            $user = new KpUser;
            $reg='/^[1-2]$/';
            if(!preg_match($reg,$arr['type'])){
               echo 3;//错误
            }else{
                if($arr['type']==1){
                   $arr2 =   $user->one('kp_user','user_Email','user_Email="'.$arr['email'].'"');//表名，字段，条件//0通过
                    echo   $arr2;
                }else
                if($arr['type']==2){
                    $arr2 =   $user->one('kp_company_register','cr_email','cr_email="'.$arr['email'].'"');//表名，字段，条件
                    echo $arr2;
                }else{
                    echo 4;
                }
            }
        }
        /**
         * 接收简历邮箱唯一性
         */
        function actionOne2(){
            $arr = Yii::$app->request->post();//接收数据
            $user = new KpUser;
            echo $user->one('kp_company_info','ci_email','ci_email="'.$arr['email'].'"');//表名，字段，条件//0通过
        }
        /**
         * @throws \yii\db\Exception
         */
        public function actionReg_att(){
            //实例化model
            $user = new KpUser;
            $kp_company_register= new KpCompanyRegister();
            $mess="";
            $arr = Yii::$app->request->get();//接收数据
            //邮箱正则
            $preg_email ='/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
            //密码正则
            $preg_pwd='/^\w{6,18}$/';
            //类型正则
            $preg_type='/^[1-2]$/';
            if(empty($arr['type'])){
                $mess="01";
            }
            else  if(!preg_match($preg_type,$arr['type'])){
                $mess="0";
            }
            else  if(empty($arr['email'])){
                $mess="1";
            }
            else if(!preg_match($preg_email,$arr['email'])){
                $mess="2";
            }
            else if(empty($arr['password'])){
                $mess="3";
            }
            else if(!preg_match($preg_pwd,$arr['password'])){
                $mess="4";
            }else{
                if($arr['type']==1){
                    $arr2 =   $user->one('kp_user','user_Email',"user_Email='$arr[email]'");//表名，字段，条件
                }else{

                    $arr2 =   $kp_company_register->one('kp_company_register','cr_email',"cr_email='$arr[email]'");//表名，字段，条件
                }
                //如果数据库有数据，
                if($arr2==1){
                    $mess="5";
                }elseif($arr2==2){
                    $mess="6";
                }elseif($arr2==0){ }
            }
            if($mess!=""){
            //验证错误
                switch ($mess) {
                    case 01:
                        echo "<script>window.alert('请选择注册类型');history.go(-1)</script>";break;die;
                    case 0:
                        echo "<script>window.alert('类型信息错误');history.go(-1)</script>";break;die;
                    case 1:
                        echo "<script>window.alert('邮箱为空');history.go(-1)</script>";break;die;
                    case 2:
                        echo "<script>window.alert('邮箱格式错误');history.go(-1)</script>";break;die;
                    case 3:
                        echo "<script>window.alert('密码为空');history.go(-1)</script>";break;die;
                    case 4:
                        echo "<script>window.alert('密码格式不正确');history.go(-1)</script>";break;die;
                    case 5:
                        echo "<script>window.alert('邮箱已经注册');history.go(-1)</script>";break;die;
                    case 6:
                        echo "<script>window.alert('数据错误');history.go(-1)</script>";break;die;
                }
            }else{
            //正确验证
                session_start();
                switch ($arr['type'])
                {
                    case 1:
                        //个人信息入库
                        $pwd_md5 =  $user->pwd($arr['email'],$arr['password']);//调用model KpUser
                        $sql = "insert into `kp_user`(`user_email`,`password`)values('$arr[email]','$pwd_md5')";
                        $bool = Yii::$app->db->createCommand($sql)->execute();
                        if($bool){
                            $info =   $user->select('kp_user','one',"user_Email='$arr[email]' and password='$pwd_md5'");//表名，字段，条件
                            $_SESSION['u_id']=$info['u_id'];
                            $_SESSION['user']=$info['u_id'];
                            if($info['type']==1){
                                $_SESSION['identity']="person";//找工作
                            }else{
                                $_SESSION['identity']="company";//找人
                            }
                          $this->redirect('index.php?r=index');//重定向

                        }else{
                            echo "<script>window.alert('注册错误');history.go(-1)</script>";break;
                        }
                        break;
                    case 2:
                        //公司信息入库
                        $time=date('Y-m-d H:i:s',time());
                        $pwd_md5 =  $kp_company_register->pwd($arr['email'],$arr['password']);//调用model KpUser
                        $sql = "insert into `kp_company_register`(`cr_email`,`password`,`cr_time`)values('$arr[email]','$pwd_md5','$time')";
                        $bool = Yii::$app->db->createCommand($sql)->execute();
                        if($bool){
                            $info =   $kp_company_register->select('kp_company_register','one',"cr_email='$arr[email]' and password='$pwd_md5'");//表名，字段，条件
                            $sql = "insert into `kp_company_info`(`cr_id`)values('$info[cr_id]')";//添加恭喜信息关联表
                            Yii::$app->db->createCommand($sql)->execute();
                            $_SESSION['cr_id']=$info['cr_id'];
                            $_SESSION['user']=$info['cr_id'];
                            if($info['type']==1){
                                $_SESSION['identity']="person";//找工作
                            }else{
                                $_SESSION['identity']="company";//找人
                            }
                            $this->redirect('index.php?r=login/bindstep1');//重定向
                        }else{
                            echo "<script>window.alert('注册错误');history.go(-1)</script>";break;
                        }
                        break;
                }
            }
        }
        /*******
        * 注册完成，补全信息
         * 第一步
        */
        public function  actionBindstep1(){//bindstep1.html
            session_start();
          empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
            $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_register')
                ->innerJoin('kp_company_info','kp_company_register.cr_id=kp_company_info.cr_id')
                ->where("kp_company_info.cr_id=$cr_id")
                ->one();//->all()出来直接是数组
            return $this->render('bindstep1.html',array('arr'=>$rows));
        }
        /*******
         * 注册完成，补全信息
         * 第一步
         */
        public function  actionBindstep2()
        {
            session_start();
            $arr = Yii::$app->request->post();//接收数据
            if(!empty($arr)){
                $sql = "update `kp_company_info` set ci_phone='$arr[tel]',ci_email='$arr[ci_email]' WHERE  cr_id='$arr[cr_id]'";
                Yii::$app->db->createCommand($sql)->execute();
                $rows = (new \yii\db\Query())
                    ->select(['*'])
                    ->from('kp_company_register')
                    ->innerJoin('kp_company_info','kp_company_register.cr_id=kp_company_info.cr_id')
                    ->where("kp_company_info.cr_id=$arr[cr_id]")
                    ->one();//->all()出来直接是数组
                return $this->render('bindstep2.html',array('arr'=>$rows));
            }else{
                empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
                if($cr_id==""){
                    return $this->render('login.html');die;
                }else{
                    $arr['cr_id']=$cr_id;
                    $rows = (new \yii\db\Query())
                        ->select(['*'])
                        ->from('kp_company_register')
                        ->innerJoin('kp_company_info','kp_company_register.cr_id=kp_company_info.cr_id')
                        ->where("kp_company_info.cr_id=$arr[cr_id]")
                        ->one();//->all()出来直接是数组
                    return $this->render('bindstep2.html',array('arr'=>$rows));
                }
            }
        }
        /*******
         * 注册完成，补全信息
         * 第3步邮箱验证
         */
        public function  actionBindstep3()
        {
            $arr = Yii::$app->request->post();//接收数据
            if (empty($arr)) {
                $this->redirect('index.php?r=index');//重定向
            } else {
                empty($_POST['companyName']) ? $companyName = "" : $companyName = $_POST['companyName'];
                if ($companyName != "") {
                    $sql = "update `kp_company_info` set ci_name='$arr[companyName]' WHERE  cr_id='$arr[cr_id]'";
                    Yii::$app->db->createCommand($sql)->execute();
                }
                $rows = (new \yii\db\Query())
                    ->select(['*'])
                    ->from('kp_company_register')
                    ->innerJoin('kp_company_info', 'kp_company_register.cr_id=kp_company_info.cr_id')
                    ->where("kp_company_info.cr_id=$arr[cr_id]")
                    ->one();//->all()出来直接是数组
                ###########################
                //  $this->actionSend_email();
                ###############################
                return $this->render('bindstep3.html', array('arr' => $rows));
            }
        }
         // function actionSend_email($email2=""){
    //        //使用163邮箱服务器
    //        $smtpserver = "ssl://smtp.qq.com";
    //        //163邮箱服务器端口
    //        $smtpserverport = 465;
    //        //你的163服务器邮箱账号
    //        $smtpusermail = "18210490369@163.com";
    //        //收件人邮箱
    //        $smtpemailto = "";
    //        //你的邮箱账号(去掉@163.com)
    //        $smtpuser = "18210490369";//SMTP服务器的用户帐号
    //        //你的邮箱密码
    //        $smtppass = ""; //SMTP服务器的用户密码
    //        //邮件主题
    //        $mailsubject = "";
    //        //邮件内容
    //        $mailbody = "项目优化之php发送email";
    //        //邮件格式（HTML/TXT）,TXT为文本邮件
    //        $mailtype = "html";
    //        //这里面的一个true是表示使用身份验证,否则不使用身份验证.
    //        $smtp = new \smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
    //        //是否显示发送的调试信息
    //        $smtp->debug = false;
    //        //发送邮件
    //        $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
    //    }

}

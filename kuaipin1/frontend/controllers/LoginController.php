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
        session_start();
        //消除session
        unset($_SESSION['user']);
        unset($_SESSION['identity']);
		return $this->render('login.html');
	}
/**
 * 注册
 */
	public function actionRegister(){
        session_start();
        unset($_SESSION['user']);
        unset($_SESSION['identity']);
        unset($_SESSION['ci_id']);

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
        $preg_username = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
        if (!preg_match($preg_username, $arr['email'])) {
            $st = 3;
        }

        $preg_pwd ='/^\w{6,18}$/';
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
                        $_SESSION['cr_id'] = $rows['cr_id'];
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

        session_start();
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
            switch ($arr['type'])
            {
                case 1:
                    //个人信息入库
                    $arr['email']=  strtolower($arr['email']);//将邮箱的所有字母转化为小写
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
                        $sql = "INSERT INTO `kuaipin`.`kp_company_info`     (`cr_id`)   VALUES('$info[cr_id]');
                                    INSERT INTO `kuaipin`.`kp_company_project`  ( `ci_id`)  VALUES ('$info[cr_id]');
                                    INSERT INTO `kuaipin`.`kp_company_people`   ( `ci_id`)  VALUES ('$info[cr_id]');";//添加恭喜信息关联表
                        Yii::$app->db->createCommand($sql)->execute();
                        $_SESSION['cr_id']=$info['cr_id'];
                        $_SESSION['user']=$info['cr_id'];
                        if($info['type']==1){
                            $_SESSION['identity']="person";//找工作
                        }else{
                            $_SESSION['identity']="company";//找人
                        }
                        $this->redirect('index.php?r=login/index01');//重定向
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

    public function  actionIndex01(){
        session_start();
        $arr = Yii::$app->request->post();//接收数据

        empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
        if(!empty($arr)){
            //如果穿的值不是空的 就入库 并且重定向
            /**
             *  `ci_phone`='13301098888',
             *
             */
            if(!empty($_FILES['ci_logo'])){
                $num= substr($_FILES['ci_logo']['name'],strrpos($_FILES['ci_logo']['name'],'.')+1) ;
                $num=   strtolower($num);
                if($num=='jpg'||$num=='png'||$num=='gif'||$num=='jpeg'||$num=='bmp'){
                    is_dir('pubimg') or mkdir('pubimg');
                    is_dir('pubimg/b/') or mkdir('pubimg/b/');
                    $img_url="pubimg/b/".date('YMD').time().'.'.$num;
                    $bool= move_uploaded_file($_FILES['ci_logo']['tmp_name'],$img_url);
                }
            }

            if(!empty($_FILES['ci_license'])){
                $num= substr($_FILES['ci_license']['name'],strrpos($_FILES['ci_license']['name'],'.')+1) ;
                $num=   strtolower($num);
                if($num=='jpg'||$num=='png'||$num=='gif'||$num=='jpeg'||$num=='bmp'){
                    is_dir('pubimg') or mkdir('pubimg');
                    is_dir('pubimg/a/') or mkdir('pubimg/a/');
                    $img1_url="pubimg/a/".date('YMD').time().'.'.$num;
                    $bool= move_uploaded_file($_FILES['ci_license']['tmp_name'],$img1_url);
                }
            }
//如果穿的值不是空的 就入库 并且重定向
            /**
             *  `ci_phone`='13301098888',
             *
             */
            empty($img_url)?$img_url="":$img_url;
            empty($img1_url)?$img1_url="":$img1_url;
            if($img_url==""&& $img1_url==""){
                $sql="UPDATE `kuaipin`.`kp_company_info` SET
    `ci_scale`='$arr[select_scale_hidden]',
    `ci_field`='$arr[select_industry_hidden]',
    `ci_name`='$arr[ci_name]',
    `ci_phone`='$arr[ci_phone]',
    `ci_email`='$arr[ci_email]',
    `ci_logo`='',
    `ci_license`='',
    `ci_ming`='$arr[name]',
    `ci_url`='$arr[website]',
    `ci_city`='$arr[ci_city]',
    `ci_develop`='$arr[s_radio_hidden]',
    `ci_intro`='$arr[con_tro]'
    WHERE (`cr_id`='$cr_id')";
            }else{
                $sql="UPDATE `kuaipin`.`kp_company_info` SET

    `ci_scale`='$arr[select_scale_hidden]',
    `ci_field`='$arr[select_industry_hidden]',
    `ci_name`='$arr[ci_name]',
     `ci_phone`='$arr[ci_phone]',
    `ci_email`='$arr[ci_email]',
    `ci_logo`='$img_url',
    `ci_license`='$img1_url',
    `ci_ming`='$arr[name]',
    `ci_url`='$arr[website]',
    `ci_city`='$arr[ci_city]',
    `ci_develop`='$arr[s_radio_hidden]',
    `ci_intro`='$arr[con_tro]'
    WHERE (`cr_id`='$cr_id')";
            }
            Yii::$app->db->createCommand($sql)->execute();
            $this->redirect('index.php?r=login/index02');//重定向
        }
        else if(!empty($cr_id)){
            //不需要use model方法
            $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_info')
                ->where("`cr_id`='$cr_id'")
                ->one();//->all()出来直接是数组，
            return $this->render('Index01.html',array('arr'=>$rows));
        }
        else{
            $this->redirect('index.php?r=login');//重定向
        }
    }

    /*******
     * 注册完成，补全信息
     * 第2步
     */
    public function  actionIndex02(){
        session_start();
        $arr = Yii::$app->request->post();//接收数据
        empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
        if(!empty($arr['data'])&&!empty($arr['cr_id'])&&$arr['cr_id']==$cr_id){
            //如果穿的值不是空的 就入库 并且重定向
            /**
             *  `正则匹配所有内容
             *
             */
            $reg='#(.*)<span>(.*)</span>(.*)#isU';
            $a="";
            foreach($arr['data'] as $key=>$val){
                preg_match_all($reg,$val,$new_val);
                $a.='，'.$new_val[2][0];
            }
            $new_data=substr($a,1);
            $sql="UPDATE `kuaipin`.`kp_company_info` SET
                    `ci_tag`='$new_data'
                    WHERE (`cr_id`='$cr_id')";
            $arr =   Yii::$app->db->createCommand($sql)->execute();
            if($arr){
                echo 1;
            }else{
                echo 0;
            }
            // $this->redirect('index.php?r=login/index03');//重定向
        }
        else if(!empty($cr_id)){
            //不需要use model方法
            $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_info')
                ->where("`cr_id`='$cr_id'")
                ->one();//->all()出来直接是数组，
            return $this->render('Index02.html',array('arr'=>$rows));
        }
        else{
            $this->redirect('index.php?r=login');//重定向
        }
        // return $this->render('index02.html');
    }

    /*******
     * 注册完成，补全信息
     * 第3步
     */
    public function  actionIndex03(){
        session_start();
        $arr = Yii::$app->request->post();//接收数据
        empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
        if(!empty($arr)){
            //如果穿的值不是空的 就入库 并且重定向
            /**
             *  `正则匹配所有内容
             *
             */
            /*
             * 此处
             * 没有
             * 验证
             */
            $num= substr($_FILES['cp_photo']['name'],strrpos($_FILES['cp_photo']['name'],'.')+1) ;
            $num=   strtolower($num);
            if($num=='jpg'||$num=='png'||$num=='gif'||$num=='jpeg'||$num=='bmp'){
                is_dir('pubimg') or mkdir('pubimg');
                $img_url="pubimg/".date('YMD').time().'.'.$num;
                $bool= move_uploaded_file($_FILES['cp_photo']['tmp_name'],$img_url);
            }
            empty($img_url)?$img_url="":$img_url;
            if($img_url==""){
                $sql="UPDATE `kuaipin`.`kp_company_people` SET
              `cp_name`='$arr[cp_name]',
              `cp_position`='$arr[cp_position]',
              `cp_weibo`='$arr[cp_weibo]',
               `cp_content`='$arr[cp_content]'
                WHERE (`ci_id`='$cr_id')";
            }else{
                $sql="UPDATE `kuaipin`.`kp_company_people` SET
              `cp_name`='$arr[cp_name]',
              `cp_position`='$arr[cp_position]',
              `cp_weibo`='$arr[cp_weibo]',
               `cp_content`='$arr[cp_content]',
               `cp_photo`='$img_url'
                WHERE (`ci_id`='$cr_id')";
            }
            $arr =   Yii::$app->db->createCommand($sql)->execute();
            $this->redirect('index.php?r=login/index04');//重定向
        }
        else if(!empty($cr_id)){
            //不需要use model方法
            $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_people')
                ->where("`ci_id`='$cr_id'")
                ->one();//->all()出来直接是数组，
            return $this->render('Index03.html',array('arr'=>$rows));
        }
        else{
            $this->redirect('index.php?r=login');//重定向
        }
    }

    /*******
     * 注册完成，补全信息
     * 第4步
     */
    public function  actionIndex04(){
        session_start();
        $arr = Yii::$app->request->post();//接收数据
        empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
        if(!empty($arr)){
            //print_r($arr);print_r($_FILES);die;
            //如果穿的值不是空的 就入库 并且重定向
            /**
             *  `正则匹配所有内容
             *
             */
            /*
             * 此处
             * 没有
             * 验证
             */
            $num= substr($_FILES['cd_photo']['name'],strrpos($_FILES['cd_photo']['name'],'.')+1) ;
            $num=   strtolower($num);
            if($num=='jpg'||$num=='png'||$num=='gif'||$num=='jpeg'||$num=='bmp'){
                is_dir('pubimg/c/') or mkdir('pubimg/c/');
                $img_url="pubimg/c/".date('YMD').time().'.'.$num;
                $bool= move_uploaded_file($_FILES['cd_photo']['tmp_name'],$img_url);
            }
            empty($img_url)?$img_url="":$img_url;
            if($img_url==""){
                $sql="UPDATE `kuaipin`.`kp_company_project` SET
                `cd_name`='$arr[cd_name]',
                `cd_url`='$arr[cd_url]',
                `cd_content`='$arr[cd_intro]'
                WHERE (`ci_id`='$cr_id');";
            }else{
                $sql="UPDATE `kuaipin`.`kp_company_project` SET
                `cd_name`='$arr[cd_name]',
                `cd_url`='$arr[cd_url]',
                `cd_photo`='$img_url',
                `cd_content`='$arr[cd_intro]'
                WHERE (`ci_id`='$cr_id');";
            }
            $arr =   Yii::$app->db->createCommand($sql)->execute();
            $this->redirect('index.php?r=login/index05');//重定向
        }
        else if(!empty($cr_id)){
            //不需要use model方法
            $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_project')
                ->where("`cd_id`='$cr_id'")
                ->one();//->all()出来直接是数组，
            $_SESSION['info_04']=$rows;
            return $this->render('Index04.html',array('arr'=>$rows));
        }
        else{
            $this->redirect('index.php?r=login');//重定向
        }
    }

    /*******
     * 注册完成，补全信息
     * 第5步
     */
    public function  actionIndex05(){
        session_start();
        $arr = Yii::$app->request->post();//接收数据
//        print_r($arr);die;
        empty($_SESSION['cr_id'])?$cr_id="":$cr_id=$_SESSION['cr_id'];
        if(!empty($arr)){
            //如果穿的值不是空的 就入库 并且重定向
            /*
             * 此处
             * 没有
             * 验证
             */
            $sql="UPDATE `kuaipin`.`kp_company_info` SET
                `ci_content`='$arr[ci_content]'
                WHERE (`cr_id`='$cr_id');";
            $arr =   Yii::$app->db->createCommand($sql)->execute();
//            print_r($arr);die;
            $this->redirect('index.php?r=index');//重定向
        }
        else if(!empty($cr_id)){
            //不需要use model方法
            $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_info')
                ->where("`cr_id`='$cr_id'")
                ->one();//->all()出来直接是数组，
            $_SESSION['info_05']=$rows;
            return $this->render('Index05.html',array('arr'=>$rows));
        }
        else{
            $this->redirect('index.php?r=login');//重定向
        }
    }
}

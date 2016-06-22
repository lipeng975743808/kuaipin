<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use app\models\KpUser;
use app\models\KpCompanyRegister;
use app\models\KpResume;

/**
 * Index controller
 */
class My_resumeController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 制作简历
 */
	public function actionMake(){
        $session = Yii::$app->session;
        $user=$session->get("user");

        $identity=$session->get("identity");
        if (empty($user) || empty($identity)) {
            $this->redirect_message('请先登陆','error',3,"index.php?r=login/index");die;
        }

        $arr = KpUser::find()
            ->where(['u_id' => $user])
            ->asArray()
            ->one();
        $resume = KpResume::find()
            ->where(['u_id' => $user])
            ->asArray()
            ->one();
        $arr[]=$resume;
        // print_r($arr);die;
        return $this->render('index.html',['arr'=>$arr]);

    }

    //修改个人信息
    public function actionUser_add()
    {
        $request = \YII::$app->request;
        $arr = $request->post();
        // print_r($arr);die;
        $u_id=$arr['u_id'];
        // print_r($u_id);die;
        $customer = KpUser::findOne($u_id);
        // print_r($customer);die;
        $customer->i_name =$arr['username'];
        $customer->i_sex = $arr['nsex'];
        $customer->i_demission_start = $arr['currentState'];
        $customer->i_experience = $arr['workyear'];
        $customer->iphone = $arr['tel'];
        $customer->user_email = $arr['email'];
        $customer->academic = $arr['topDegree'];
        $res = $customer->update();
        // echo $res;
    }

    //期望工作
    public function actionExpectwork()
    {
         $session = Yii::$app->session;
         $u_id = $session->get('user');
        $request = \YII::$app->request;
        $arr = $request->post();
        // print_r($arr);die;
        $customer = KpResume::findOne(['u_id'=>$u_id]);
        // print_r($customer);die;
        $customer->re_ecpect_crty = $arr['expectcity'];
        $customer->re_ecpect_type = $arr['posttype'];
        $customer->re_ecpect_salary = $arr['expectmoney'];
        $customer->re_ecpect_job = $arr['position'];
        if ($customer->save())
        {
            echo 1;
        }
    }

    //工作经历
    public function actionCompanyjl()
    {
        $session = Yii::$app->session;
        $u_id = $session->get('user');

        $request = \YII::$app->request;
        $arr = $request->post();
        // print_r($arr);die;
        //开始时间
        $starttime = $arr['yearstart'].'.'.$arr['monthstart'];
        // print_r($startime);die;
        //结束时间
        $endtime = $arr['yearend'].'.'.$arr['monthend'];
        $customer = KpResume::findOne(['u_id'=>$u_id]);
        $customer->company_name=$arr['companyname'];
        $customer->position_name=$arr['positionname'];
        $customer->Company_begin_time=$starttime;
        $customer->Company_add_time=$endtime;

        if ($customer->save())
        {
            echo 1;
        }
    }

    //项目经验
    public function actionProject()
    {
        $session = Yii::$app->session;
        $u_id = $session->get('user');

        $request = \YII::$app->request;
        $arr = $request->post();
        //开始年份
        $time_start = $arr['project_start'].'.'.$arr['project_month_start'];
        //结束年份
        $time_end = $arr['project_end'].'.'.$arr['project_month_end'];
        $customer = KpResume::findOne(['u_id'=>$u_id]);
        $customer->Project_begin_time = $time_start;
        $customer->Project_add_time = $time_end;
        $customer->project_name = $arr['projectname'];
        $customer->post_job = $arr['post_job'];
        $customer->Project_content = $arr['project_content'];
        // print_r($arr);die;
        if ($customer->save())
        {
            echo 1;
        }
    }

    //教育背景
    public function actionJiaoyu()
    {
         $session = Yii::$app->session;
         $u_id = $session->get('user');
        $request = \YII::$app->request;
        $arr = $request->post();
        // print_r($arr);
        $customer = KpUser::findOne(['u_id'=>$u_id]);
        $customer->academic=$arr['hi_degree'];
        $customer->major_name=$arr['schoolname'];
        $customer->school_year_start=$arr['hi_school_start'];
        $customer->school_year_end=$arr['hi_school_end'];
        $customer->professionalname=$arr['professionalname'];

        if ($customer->save())
        {
            echo 1;
        }
    }

    //自我描述
    function actionDescription()
    {
         $session = Yii::$app->session;
         $u_id = $session->get('user');
        $request = \YII::$app->request;
        $arr = $request->post();
        // print_r($arr);die;
        $customer = KpResume::findOne(['u_id'=>$u_id]);
        $customer->me_content = $arr['selfdescription'];
        if ($customer->save())
        {
            echo 1;
        }
    }

    //作品展示
    public function actionWorks()
    {
         $session = Yii::$app->session;
         $u_id = $session->get('user');
        $request = \YII::$app->request;
        $arr = $request->post();
        // print_r($arr);die;
        $customer = KpResume::findOne(['u_id'=>$u_id]);
        $customer->Works_href = $arr['worklink'];
        $customer->Works_title = $arr['workedscription'];

        if ($customer->save())
        {
            echo 1;
        }
    }
    /**
* 预览简历
*/
	public function actionPreview(){
        $session = Yii::$app->session;
         $u_id = $session->get('user');
        $arr = KpUser::find()
            ->where(['u_id' => $u_id])
            ->asArray()
            ->one();
        $resume = KpResume::find()
            ->where(['u_id' => $u_id])
            ->asArray()
            ->one();
        $arr[]=$resume;
        // print_r($arr);die;
        return $this->render('preview.html',['arr'=>$arr]);
	}

    function actionHead()
    {
        $session = Yii::$app->session;
        $arr['sess_uid'] = $session->get('user');
        $arr['sess_idc'] = $session->get('identity');

        if (!empty($arr['sess_uid']) && !empty($arr['sess_idc'])) {
            if ($arr['sess_idc'] == "company") {
                //查询出公司用户的邮箱
                $user_info = KpCompanyRegister::find()
                    ->where(['cr_id' => $arr['sess_uid']])
                    ->asArray()
                    ->one();
                $arr['uemail'] = $user_info['cr_email'];
            } else {
                //查询出个人用户的邮箱
                $user_info = KpUser::find()
                    ->where(['u_id' => $arr['sess_uid']])
                    ->asArray()
                    ->one();
                $arr['uemail'] = $user_info['user_email'];
            }
        }
        return $arr;
    }

}
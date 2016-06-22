<?php
namespace frontend\controllers;
header('content-type:text/html;charset=utf8');
use app\models\KpUser;
use app\models\KpCompanyRegister;
use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class IndexController extends Controller
{
    public $enableCsrfValidation = false;
    /* 去除Yii框架格式 */
    public $layout = false;

    /**
     * 主页面
     */
    public function actionIndex()
    {
        $list = $this->actionGetTree();
        $sql = "select * from kp_ad as a join kp_company_info as b on b.ci_id=a.ci_id limit 6";
        $ad = Yii::$app->db->createCommand($sql)->queryAll();
        $sql1 = "select * from kp_company_info as a join kp_company_job as b on b.ci_id=a.ci_id order by cj_num desc limit 15";
        $work = Yii::$app->db->createCommand($sql1)->queryAll();
        $sql2 = "select * from kp_company_info as a join kp_company_job as b on b.ci_id=a.ci_id order by cj_id desc limit 15";
        $work1 = Yii::$app->db->createCommand($sql2)->queryAll();
        return $this->render('index.html',['arr'=>$list,'ad'=>$ad,'work'=>$work,'work1'=>$work1]);
    }
    function actionGetTree($pid=0){
        $arr = Yii::$app->db->createCommand("select * from kp_work where pid='$pid'")->queryAll();
        foreach ($arr as $key => $value) {
            $arr[$key]['son'] = $this->actionGetTree($value['w_id']);
        }
        return $arr;
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
    /**
     * 搜索职位
     */
    public function actionSearch(){
        return $this->render('list.html');
    }
}
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

        return $this->render('index.html',['arr'=>$list]);
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
}
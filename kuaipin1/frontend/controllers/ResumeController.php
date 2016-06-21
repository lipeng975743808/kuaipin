<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use app\models\KpUser;
use app\models\KpCompanyRegister;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

/**
 * Index controller
 */
class ResumeController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 简历列表
 */
	public function actionList(){
        $session = Yii::$app->session;
        $user=$session->get("user");
        $identity=$session->get("identity");
        if (empty($user) || empty($identity)) {
            $this->redirect_message('请先登陆','error',3,"index.php?r=login/index");die;
        }
		return $this->render('index.html');
	} 
/**
* 我要招人
*/
	public function actionAdd_people(){
		return $this->render('index.html');
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


    //

}
<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use app\models\KpUser;
use app\models\KpCompanyRegister;


/**
 * Index controller
 */
class Company_listController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 公司
 */
	public function actionIndex(){
        //查询公司信息
        $rows = (new \yii\db\Query())
            ->select(['*'])
            ->from('kp_company_info')
            ->all();
//        print_r($rows);die;
        foreach($rows as $k=>$v){
            $rows[$k]['ci_tag']=explode('，',$v['ci_tag']);
            $jobs = (new \yii\db\Query())
                ->select(['cj_name','cj_id'])
                ->from('kp_company_job')
                ->where(['ci_id'=>$v['ci_id']])
                ->all();
            $rows[$k]['ci_job']=$jobs;
        }
//        print_r($rows);die;
		return $this->render('index.html',['info'=>$rows]);
	}
/**
* 公司
*/
    public function actionIndexs(){
        return $this->render('myhome.html');
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
     * 职位详情页
     */
    public function actionShow(){
        $id = $_GET['id'];
        $sql = "select * from kp_company_job as a join kp_company_info as b on b.ci_id=a.ci_id where cj_id='$id'";
        $arr = Yii::$app->db->createCommand($sql)->queryAll();
        return $this->render('content.html',['list'=>$arr]);
    }

    /**
     * 职位（简历）详情页
     */
    public function actionYanzheng(){
        session_start();
        $id = $_POST['id'];
        $user_id = $_SESSION['user'];
        $sql = "select re_id from kp_resume where u_id='$user_id'";
        $arr = Yii::$app->db->createCommand($sql)->queryOne();
        $re_id = $arr['re_id'];
        $sql1 = "select * from kp_resume_company where re_id='$re_id' AND ci_id='$id'";
        $row = Yii::$app->db->createCommand($sql1)->queryOne();
        if($row){
            echo 1;
        }else{
            $sql1 = "insert into kp_resume_company(re_id,ci_id) values('$re_id','$id')";
            $rows = Yii::$app->db->createCommand($sql1)->execute();
            if($rows){
                echo 2;
            }else{
                echo 3;
            }
        }
    }

    /**
     * 公司详情页
     */
    public function actionHome(){
        $request = Yii::$app->request;
        $cid=$request->get('cid');
        $rows = (new \yii\db\Query())
            ->select(['a.ci_id',
                        'ci_name',
                        'ci_logo',
                        'ci_tag',
                        'ci_city',
                        'ci_phone',
                        'ci_field',
                        'ci_scale',
                        'ci_develop',
                        'ci_url',
                        'ci_content',
                        'cd_name',
                        'cd_photo',
                        'cd_content',
                        'cp_name',
                        'cp_content',
                        'cp_photo',
                        'cp_position',
                        'cp_weibo',
            ])
            ->from('kp_company_info AS a')
            ->leftJoin('kp_company_project AS d','d.ci_id = a.ci_id')
            ->leftJoin('kp_company_people AS p','p.ci_id = a.ci_id')
            ->where(['a.ci_id' => $cid,])
            ->one();

        //查询新闻
        $rows1 = (new \yii\db\Query())
            ->select(['news_title','news_content'])
            ->from('kp_news')
            ->where(['ci_id'=>$cid])
            ->orderBy('news_id DESC')
            ->limit(2)
            ->all();

        //查询职位
        $rows2 = (new \yii\db\Query())
            ->select(['cj_low_money','cj_high_money','cj_experience','cj_degree','cj_character','cj_name','cj_add_time'])
            ->from('kp_company_job')
            ->where(['ci_id'=>$cid])
            ->orderBy('cj_id DESC')
            ->all();
        return $this->render('home.html',['info'=>$rows,'news'=>$rows1,'job'=>$rows2]);
    }
}
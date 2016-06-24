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
        $request=Yii::$app->request;
        $w_name=$request->get('w_name');
        $row= (new\yii\db\Query())
            ->select(['*'])
            ->from('kp_company_job')
            ->innerJoin('kp_company_info',' kp_company_job.ci_id=kp_company_info.ci_id')
            ->innerJoin('kp_company_people','kp_company_people.ci_id=kp_company_info.ci_id')
            ->where("kp_company_job.cj_name like '%$w_name%' and kp_company_info.ci_id=kp_company_people.ci_id")
            ->all();//->one()出来直接是数组
        //获取当前页
        $p=empty($_GET['p']) ? 1 : $_GET['p'];
        //设置每页条数
        $size=1;
        //获取总条数
        $num=count($row);
        //获取总页数
        $pages=ceil($num/$size);
        //计算偏移量
        $pyl=($p-1)*$size;
        //上一页 下一页
        $up=$p-1<=1 ? 1 : $p-1;
        $down=$p+1  >=$pages ? $pages : $p+1;
        $str='';
        $str.='<a href="javascript:onclick=pager(1)">首页</a>';
        $str.='<a href="javascript:onclick=pager('.$up.')">上一页</a>';
        $str.='<a href="javascript:onclick=pager('.$down.')">下一页</a>';
        $str.='<a href="javascript:onclick=pager('.$pages.')">尾页</a>';
        $rows = (new\yii\db\Query())
            ->select(['*'])
            ->from('kp_company_job')
            ->innerJoin('kp_company_info',' kp_company_job.ci_id=kp_company_info.ci_id')
            ->innerJoin('kp_company_people','kp_company_people.ci_id=kp_company_info.ci_id')
            ->offset('$pyl')
            ->limit('1')
            ->where("kp_company_job.cj_name like '%$w_name%' and kp_company_info.ci_id=kp_company_people.ci_id")
            ->all();//->one()出来直接是数组
        return $this->render('list.html',['arr'=>$rows,'str'=>$str]);
    }
}
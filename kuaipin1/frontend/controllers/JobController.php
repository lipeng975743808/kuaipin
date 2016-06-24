<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use app\models\KpWork;
use app\models\KpUser;
use app\models\KpCompanyRegister;

/**
 * Index controller
 */
class JobController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 *  发布职位
 */
	public function actionJob_make(){
		$session = Yii::$app->session;
        $user=$session->get("user");
        $identity=$session->get("identity");
        if (empty($user) || empty($identity)) {
            $this->redirect_message('请先登陆','error',3,"index.php?r=login/index");die;
        }
		 $sql="select * from kp_work";
        $arr=Yii::$app->db->createCommand($sql)->queryAll();
        $ar=$this->actionCate($arr,0,0);
        //print_r($ar);
	    return $this->render('index.html',['a'=>$ar]);
    }

 public function actionCate(&$info, $child, $pid)  
{  
    $child = array();  
    if(!empty($info)){//当$info中的子类还没有被移光的时候  
        foreach ($info as $k => &$v) {  
            if($v['pid'] == $pid){//判断是否存在子类pid和返回的父类id相等的  
                $v['child'] = $this->actionCate($info, $child, $v['w_id']);//每次递归参数为当前的父类的id  
                $child[] = $v;//将$info中的键值移动到$child当中  
                unset($info[$k]);//每次移动过去之后删除$info中当前的值  
            }  
        }  
    }  
    return $child;//返回生成的树形数组  
}   
/*接收发布职位*/
public function actionAdd()
{
    $request=Yii::$app->request;
    //获取登录人id 
    $data['ci_id']=1;
    $data['cj_name']=$request->post('cj_name');
    $data['cj_type']=$request->post('cj_type');
    $data['cj_character']=$request->post('cj_character');
    $data['cj_low_money']=$request->post('cj_low_money');
    $data['cj_high_money']=$request->post('cj_high_money');
    $data['cj_city']=$request->post('cj_city');
    $data['cj_experience']=$request->post('cj_experience');
    $data['cj_degree']=$request->post('cj_degree');
    $data['cj_positionAdvantage']=$request->post('cj_positionAdvantage');
    $data['cj_position']=$request->post('cj_position');
    $data['cj_rec_email']=$request->post('cj_rec_email');
    $data['cj_add_time']=date('Y-m-d',time());
    $row = Yii::$app->getDb()->createCommand()->insert('kp_company_job',$data)->execute();
    if($row)
    {
        echo "<script>alert('添加成功');location.href='index.php?r=job/positions'</script>";
    }
    else
    {
        echo "<script>alert('添加失败')</script>";
    }
}
/**
*  有效职位
*/
	public function actionPositions(){
        //通过session 获取用户id
        $cr_id=Yii::$app->session['user'];
        $sql="select * from kp_company_info where cr_id=$cr_id";
        $row=Yii::$app->db->createCommand($sql)->queryOne();
        $ci_id=$row['ci_id'];
        //通过session id 查询职位表
        // $sql="select * from kp_company_job where ci_id=$ci_id";
        $rows = (new\yii\db\Query())
            ->select(['*'])
            ->from('kp_company_job')
            ->innerJoin('kp_company_info',' kp_company_job.ci_id=kp_company_info.ci_id')
            ->where("kp_company_job.ci_id='$ci_id'&kp_company_job.wire=1")
            ->all();//->one()出来直接是数组，
        $count=count($rows);
        return $this->render('positions.html',['ar'=>$rows,'counts'=>$count]);
	} 
    /**修改在线职位*/
    public function actionUpdatewire()
    {
        $request=Yii::$app->request;
        //接受修改在线的字段
        $wire=$request->get('wi');
        //接受需要修改的id
        $cj_id=$request->get('cj_id');

        // $post=new Kp_Company_job;  
        // $post->wire='0';  
        // $post->save();

        // $customer =Kp_Company_job::findOne($cj_id);
        // $customer->wire = '0';
        // $customer->save();
        $sql="update kp_company_job set wire=0 where cj_id=$cj_id";
        $res=Yii::$app->db->createCommand($sql)->execute();
        if ($res) {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }
    public function actionDeletes()
    {
        $request=Yii::$app->request;
        $cj_id=$request->get('id');
        $sql="delete from kp_company_job where cj_id=$cj_id";
        $res=Yii::$app->db->createCommand($sql)->execute();
        if($res)
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }

    public function actionRefresh()
    {
        $request=Yii::$app->request;
        $cj_id=$request->get('cj_id');
        //获取当前时间戳
        $time=time();
        $sql="select * from kp_company_job where cj_id=$cj_id";
        $row=Yii::$app->db->createCommand($sql)->queryOne();

        //获取本条数据上次刷新的时间戳
        $times=$row['times'];
        //判断如果时间戳为空那么就进行入库
        if(empty($times))
        {
            $sql="update kp_company_job set times=$time where cj_id=$cj_id";
            $rows=Yii::$app->db->createCommand($sql)->execute();
            die;
        }
        /*
          *如果有值的话就拿数据库的值跟当前时间戳比较如果大于七天那么就允许刷新 否则给出提示
        */
        else
        {
            //获取上次刷新时的时间
            $cj_add_time=$row['times'];
            $a=60*60*24*7;
            $timess=strtotime(date('Y-m-d H:i:s',time()+60*60*24*7))-strtotime(date('Y-m-d H:i:s',time()));
            if($time-$cj_add_time<$timess)
            {
                echo 1;
                die;
            }
            else
            {
                $sql="update kp_company_job set times=$time where cj_id=$cj_id";
                $rows=Yii::$app->db->createCommand($sql)->execute();
            }
        }
    }

    public function actionUpdates()
    {
        $sql="select * from kp_work";
        $arr=Yii::$app->db->createCommand($sql)->queryAll();
        $ar=$this->actionCate($arr,0,0);
        $request=Yii::$app->request;
        $cj_id=$request->get('id');
        $sql="select * from kp_company_job where cj_id=$cj_id";
        $row=Yii::$app->db->createCommand($sql)->queryOne();
        // print_R($row);die;
        return $this->render('updates.html',['ar'=>$row,'a'=>$ar]);
    }

    //修改有效职位
    public function actionUpdatezw()
    {
        $request=Yii::$app->request;
        $cj_id=$request->post('cj_id');
        $cj_name=$request->post('cj_name');
        $cj_type=$request->post('cj_type');
        $cj_character=$request->post('cj_character');
        $cj_low_money=$request->post('cj_low_money');
        $cj_high_money=$request->post('cj_high_money');
        $cj_city=$request->post('cj_city');
        $cj_experience=$request->post('cj_experience');
        $cj_degree=$request->post('cj_degree');
        $cj_positionAdvantage=$request->post('cj_positionAdvantage');
        $cj_position=$request->post('cj_position');
        $cj_rec_email=$request->post('cj_rec_email');
        $sql="update kp_company_job set cj_name='$cj_name',cj_type='$cj_type',cj_character='$cj_character',cj_low_money='$cj_low_money',cj_high_money='$cj_high_money',cj_city='$cj_city',cj_experience='$cj_experience',cj_positionAdvantage='$cj_positionAdvantage',cj_position='$cj_position',cj_rec_email='$cj_rec_email' where cj_id=$cj_id";
        $ar=Yii::$app->db->createCommand($sql)->execute();
        if ($ar) {
            echo "<script>alert('操作成功');location.href='index.php?r=job/positions'</script>";
        }
        else
        {
            echo 0;
        }
    }




/**
*  已下线职位
*/
	public function actionNopositions(){
        //通过session 获取用户id
        $cr_id=Yii::$app->session['user'];
        $sql="select * from kp_company_info where cr_id=$cr_id";
        $row=Yii::$app->db->createCommand($sql)->queryOne();
        $ci_id=$row['ci_id'];
        //通过session id 查询职位表
        // $sql="select * from kp_company_job where ci_id=$ci_id";
        $rows = (new\yii\db\Query())
            ->select(['*'])
            ->from('kp_company_job')
            ->innerJoin('kp_company_info',' kp_company_job.ci_id=kp_company_info.ci_id')
            ->where("kp_company_job.ci_id='$ci_id'&kp_company_job.wire=0")
            ->all();//->one()出来直接是数组，
        $count=count($rows);
        return $this->render('positions1.html',['ar'=>$rows,'counts'=>$count]);
    }

    /**修改在线职位*/
    public function actionUpdatewire1()
    {
        $request=Yii::$app->request;
        //接受修改在线的字段
        $wire=$request->get('wi');
        //接受需要修改的id
        $cj_id=$request->get('cj_id');

        // $post=new Kp_Company_job;
        // $post->wire='0';
        // $post->save();

        // $customer =Kp_Company_job::findOne($cj_id);
        // $customer->wire = '0';
        // $customer->save();
        $sql="update kp_company_job set wire=1 where cj_id=$cj_id";
        $res=Yii::$app->db->createCommand($sql)->execute();
        if ($res) {
            echo 1;
        }
        else
        {
            echo 0;
        }

    }

    public function actionDeletes1()
    {
        $request=Yii::$app->request;
        $cj_id=$request->get('id');
        $sql="delete from kp_company_job where cj_id=$cj_id";
        $res=Yii::$app->db->createCommand($sql)->execute();
        if($res)
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }

    public function actionUpdates1()
    {

        $sql="select * from kp_work";
        $arr=Yii::$app->db->createCommand($sql)->queryAll();
        $ar=$this->actionCate($arr,0,0);
        $request=Yii::$app->request;
        $cj_id=$request->get('id');
        $sql="select * from kp_company_job where cj_id=$cj_id";
        $row=Yii::$app->db->createCommand($sql)->queryOne();
        // print_R($row);die;
        return $this->render('updates.html',['ar'=>$row,'a'=>$ar]);

    }

    //修改有效职位
    public function actionUpdatezw1()
    {
        $request=Yii::$app->request;
        $cj_id=$request->post('cj_id');
        $cj_name=$request->post('cj_name');
        $cj_type=$request->post('cj_type');
        $cj_character=$request->post('cj_character');
        $cj_low_money=$request->post('cj_low_money');
        $cj_high_money=$request->post('cj_high_money');
        $cj_city=$request->post('cj_city');
        $cj_experience=$request->post('cj_experience');
        $cj_degree=$request->post('cj_degree');
        $cj_positionAdvantage=$request->post('cj_positionAdvantage');
        $cj_position=$request->post('cj_position');
        $cj_rec_email=$request->post('cj_rec_email');
        $sql="update kp_company_job set cj_name='$cj_name',cj_type='$cj_type',cj_character='$cj_character',cj_low_money='$cj_low_money',cj_high_money='$cj_high_money',cj_city='$cj_city',cj_experience='$cj_experience',cj_positionAdvantage='$cj_positionAdvantage',cj_position='$cj_position',cj_rec_email='$cj_rec_email' where cj_id=$cj_id";
        $ar=Yii::$app->db->createCommand($sql)->execute();
        if ($ar) {
            echo "<script>alert('操作成功');location.href='index.php?r=job/positions1'</script>";
        }
        else
        {
            echo 0;
        }
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

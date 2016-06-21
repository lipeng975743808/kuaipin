<?php
namespace frontend\controllers;
header('content-type:text/html;charset=utf-8');
use Yii; 
use yii\web\Controller;
use app\models\KpWork;

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
        // $ci_id=$_SESSION['ci_id'];
        $ci_id=1;
        //通过session id 查询职位表
        // $sql="select * from kp_company_job where ci_id=$ci_id";
        $rows = (new \yii\db\Query())
                ->select(['*'])
                ->from('kp_company_job')
                ->innerJoin('kp_company_info',' kp_company_job.ci_id=kp_company_info.ci_id')
                ->where("kp_company_job.ci_id='$ci_id'&kp_company_job.wire=1")
                ->all();//->one()出来直接是数组，
        // $arr = Yii::$app->db->createCommand($sql)->queryAll();
		return $this->render('positions.html',['ar'=>$rows]);
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
/**
*  已下线职位
*/
	public function actionNopositions(){
		return $this->render('positions.html');
	} 
}
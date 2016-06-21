<?php

namespace app\models;

use Yii;
use yii\base\Model;
/**
 * This is the model class for table "kp_work".
 *
 * @property integer $w_id
 * @property string $w_name
 * @property integer $pid
 */
class KpWork extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kp_work';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid'], 'integer'],
            [['w_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'w_id' => 'W ID',
            'w_name' => 'W Name',
            'pid' => 'Pid',
        ];
    }

//     public function cateFind()  
// {  
//      $cateFind = new KpWork;  
//     //按照归属类排序  
//     $cateInfo =$cateFind->findAll(array(  
//         'select' => array('w_id', 'pid', 'w_name'),  
//         'order' => 'w_id'));
//     foreach ($cateInfo as $k => $v) {  
//         $cateInfo[$k] = $cateInfo[$k]->attributes;
//     }  
//     return $cateInfo;  
// }
    // print_R($cateInfo);die;
    // $cateInfos=(new \yii\db\Query())
    // ->select('*')
    // ->from('kp_work')
    // ->orderBy("w_id")
    // ->all();
    // $cateInfo=json_encode($cateInfos);
    // $cateInfoss=json_decode($cateInfo);
  
     
    // $sql="select * from kp_work order by w_id desc";
    // $cateInfo=Yii::$app->db->createCommand($sql)->queryAll();
    // print_r($cateInfo);die;
}

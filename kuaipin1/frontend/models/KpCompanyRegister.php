<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kp_company_register".
 *
 * @property integer $cr_id
 * @property string $cr_email
 * @property string $password
 * @property string $cr_time
 * @property integer $cr_job_nums
 */
class KpCompanyRegister extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kp_company_register';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cr_time'], 'safe'],
            [['cr_job_nums'], 'integer'],
            [['cr_email', 'password'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cr_id' => 'Cr ID',
            'cr_email' => 'Cr Email',
            'password' => 'Password',
            'cr_time' => 'Cr Time',
            'cr_job_nums' => 'Cr Job Nums',
        ];
    }


    /**
     * 验证唯一性函数
     * $table表名  例 kp_user
     * $info 字段例 email
     * $wherr 条件   id=1 and name=zahngsan
     *
     * @return   0 数据库没有，允许通过 1数据库已存在 不允许存在   2没有表名
     */
    function one($table,$info="*",$where=1){
        if($table) {
            $rows = (new \yii\db\Query())
                ->select(["$info"])
                ->from($table)
                ->where("$where")
                ->one();
            if (empty($rows)) {
                return 0;
            } else {
                return 1;
            }
        }else{
            return 2;//没有表名
        }
    }

    /**
     * 加密
     */
    public  function  pwd($user_Email,$password){
        return md5(sha1($password) . "kuaipin" . sha1($user_Email));//加密过的
    }

    /**
     * 查询
     * 表名   类型    w条件
     */
    function select($table,$type,$where=1,$info="*"){
        if($table) {

            if($type=="one"){
                $rows = (new \yii\db\Query())
                    ->select(["$info"])
                    ->from($table)
                    ->where("$where")
                    ->one();
            }else if($type=="all"){
                $rows = (new \yii\db\Query())
                    ->select(["$info"])
                    ->from($table)
                    ->where("$where")
                    ->all();
            }

            if (empty($rows)) {
                return "没有数据";
            } else {
                return $rows;
            }
        }else{
            return "表名为空";//没有表名
        }
    }
}

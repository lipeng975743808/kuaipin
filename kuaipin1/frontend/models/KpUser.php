<?php

namespace app\models;

use Yii;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "kp_user".
 *
 * @property integer $u_id
 * @property string $username
 * @property string $user_number
 * @property string $password
 * @property string $user_Email
 * @property string $images
 * @property string $birth_date
 * @property string $academic
 * @property string $i_name
 * @property string $i_age
 * @property string $i_sex
 * @property string $i_native
 * @property string $i_area
 * @property string $i_demission_start
 * @property string $i_experience
 * @property string $iphone
 * @property string $registration_date
 * @property string $last_login_date
 * @property string $registration_ip
 * @property string $last_login_ip
 * @property string $title
 */
class KpUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kp_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['birth_date', 'registration_date', 'last_login_date'], 'safe'],
            [['username', 'user_number', 'password', 'user_Email', 'images', 'academic', 'i_name', 'i_native', 'i_area', 'i_experience', 'iphone', 'title'], 'string', 'max' => 100],
            [['i_age', 'i_sex', 'i_demission_start'], 'string', 'max' => 10],
            [['registration_ip', 'last_login_ip'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_id' => 'U ID',
            'username' => 'Username',
            'user_number' => 'User Number',
            'password' => 'Password',
            'user_Email' => 'User  Email',
            'images' => 'Images',
            'birth_date' => 'Birth Date',
            'academic' => 'Academic',
            'i_name' => 'I Name',
            'i_age' => 'I Age',
            'i_sex' => 'I Sex',
            'i_native' => 'I Native',
            'i_area' => 'I Area',
            'i_demission_start' => 'I Demission Start',
            'i_experience' => 'I Experience',
            'iphone' => 'Iphone',
            'registration_date' => 'Registration Date',
            'last_login_date' => 'Last Login Date',
            'registration_ip' => 'Registration Ip',
            'last_login_ip' => 'Last Login Ip',
            'title' => 'Title',
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
     * //
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

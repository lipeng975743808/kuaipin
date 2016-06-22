<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "kp_resume".
 *
 * @property integer $re_id
 * @property integer $u_id
 * @property string $re_name
 * @property string $re_ecpect_job
 * @property string $company_name
 * @property string $Company_begin_time
 * @property string $Company_add_time
 * @property string $position_name
 * @property string $project_name
 * @property string $Project_begin_time
 * @property string $Project_add_time
 * @property string $Project_content
 * @property string $Major_name
 * @property string $me_content
 * @property string $Works_href
 * @property string $Works_title
 * @property string $re_add_time
 * @property integer $re_status
 * @property integer $re_looks
 */
class KpResume extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kp_resume';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_id', 're_status', 're_looks'], 'integer'],
            [['Company_begin_time', 'Company_add_time', 'Project_begin_time', 'Project_add_time', 're_add_time'], 'safe'],
            [['Project_content', 'me_content'], 'string'],
            [['re_name', 'position_name', 'project_name', 'Major_name', 'Works_href', 'Works_title'], 'string', 'max' => 100],
            [['re_ecpect_job'], 'string', 'max' => 255],
            [['company_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            're_id' => 'Re ID',
            'u_id' => 'U ID',
            're_name' => 'Re Name',
            're_ecpect_job' => 'Re Ecpect Job',
            'company_name' => 'Company Name',
            'Company_begin_time' => 'Company Begin Time',
            'Company_add_time' => 'Company Add Time',
            'position_name' => 'Position Name',
            'project_name' => 'Project Name',
            'Project_begin_time' => 'Project Begin Time',
            'Project_add_time' => 'Project Add Time',
            'Project_content' => 'Project Content',
            'Major_name' => 'Major Name',
            'me_content' => 'Me Content',
            'Works_href' => 'Works Href',
            'Works_title' => 'Works Title',
            're_add_time' => 'Re Add Time',
            're_status' => 'Re Status',
            're_looks' => 'Re Looks',
        ];
    }
}

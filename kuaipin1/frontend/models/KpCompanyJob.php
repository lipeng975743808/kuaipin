<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kp_company_job".
 *
 * @property integer $cj_id
 * @property integer $ci_id
 * @property string $cj_name
 * @property string $cj_type
 * @property string $cj_character
 * @property string $cj_city
 * @property integer $cj_low_money
 * @property integer $cj_high_money
 * @property string $cj_experience
 * @property string $cj_degree
 * @property string $cj_prospect
 * @property string $cj_content
 * @property string $cj_position
 * @property string $cj_rec_email
 * @property integer $cj_status
 * @property integer $cj_num
 * @property string $cj_add_time
 * @property integer $k_id
 * @property string $cj_positionAdvantage
 * @property string $wire
 */
class KpCompanyJob extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kp_company_job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ci_id', 'cj_low_money', 'cj_high_money', 'cj_status', 'cj_num', 'k_id'], 'integer'],
            [['cj_content'], 'string'],
            [['cj_add_time'], 'safe'],
            [['cj_name', 'cj_type'], 'string', 'max' => 20],
            [['cj_character', 'cj_city', 'cj_experience', 'cj_degree', 'cj_prospect', 'cj_position', 'cj_rec_email'], 'string', 'max' => 50],
            [['cj_positionAdvantage', 'wire'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cj_id' => 'Cj ID',
            'ci_id' => 'Ci ID',
            'cj_name' => 'Cj Name',
            'cj_type' => 'Cj Type',
            'cj_character' => 'Cj Character',
            'cj_city' => 'Cj City',
            'cj_low_money' => 'Cj Low Money',
            'cj_high_money' => 'Cj High Money',
            'cj_experience' => 'Cj Experience',
            'cj_degree' => 'Cj Degree',
            'cj_prospect' => 'Cj Prospect',
            'cj_content' => 'Cj Content',
            'cj_position' => 'Cj Position',
            'cj_rec_email' => 'Cj Rec Email',
            'cj_status' => 'Cj Status',
            'cj_num' => 'Cj Num',
            'cj_add_time' => 'Cj Add Time',
            'k_id' => 'K ID',
            'cj_positionAdvantage' => 'Cj Position Advantage',
            'wire' => 'Wire',
        ];
    }
}

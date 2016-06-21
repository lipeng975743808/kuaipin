<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kp_company_info".
 *
 * @property integer $ci_id
 * @property string $ci_phone
 * @property string $ci_email
 * @property string $ci_scale
 * @property string $ci_field
 * @property string $ci_name
 * @property string $ci_logo
 * @property string $ci_ming
 * @property string $ci_tag
 * @property string $ci_url
 * @property string $ci_city
 * @property string $ci_develop
 * @property string $ci_content
 * @property string $ci_license
 */
class KpCompanyInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kp_company_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ci_content'], 'string'],
            [['ci_phone', 'ci_email', 'ci_field', 'ci_name', 'ci_logo'], 'string', 'max' => 100],
            [['ci_scale', 'ci_ming', 'ci_tag', 'ci_url', 'ci_license'], 'string', 'max' => 50],
            [['ci_city', 'ci_develop'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ci_id' => 'Ci ID',
            'ci_phone' => 'Ci Phone',
            'ci_email' => 'Ci Email',
            'ci_scale' => 'Ci Scale',
            'ci_field' => 'Ci Field',
            'ci_name' => 'Ci Name',
            'ci_logo' => 'Ci Logo',
            'ci_ming' => 'Ci Ming',
            'ci_tag' => 'Ci Tag',
            'ci_url' => 'Ci Url',
            'ci_city' => 'Ci City',
            'ci_develop' => 'Ci Develop',
            'ci_content' => 'Ci Content',
            'ci_license' => 'Ci License',
        ];
    }
}

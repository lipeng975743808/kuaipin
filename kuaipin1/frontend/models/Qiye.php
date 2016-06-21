<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "qiye".
 *
 * @property integer $q_id
 * @property string $q_name
 * @property string $q_pf
 */
class Qiye extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qiye';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['q_name', 'q_pf'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'q_id' => 'Q ID',
            'q_name' => 'Q Name',
            'q_pf' => 'Q Pf',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "isg.tanada".
 *
 * @property int $id
 * @property string|null $geom
 * @property float|null $area 面積 m2
 * @property string|null $p_no 地番
 * @property string|null $owner 所有者
 * @property string|null $cultivator 耕作者・管理者
 * @property string|null $usage 農地利用状況
 */
class Tanada extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'isg.tanada';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'area', 'p_no', 'owner', 'cultivator', 'usage'], 'default', 'value' => null],
            [['geom'], 'string'],
            [['area'], 'number'],
            [['p_no', 'usage'], 'string', 'max' => 32],
            [['owner', 'cultivator'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'geom' => 'Geom',
            'area' => '面積',
            'p_no' => '番地',
            'owner' => '所有者',
            'cultivator' => '耕作者',
            'usage' => '作付状況',
        ];
    }
}

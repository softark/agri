<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "isg.forest".
 *
 * @property int $id
 * @property string|null $geom
 * @property string|null $p_no
 * @property string|null $o_aza
 * @property string|null $ko_aza
 * @property string|null $type
 * @property string|null $owner
 * @property string|null $o_addr
 * @property string|null $m_addr
 * @property string|null $manager
 * @property float|null $area
 * @property string|null $memo
 * @property string|null $o_type
 */
class Forest extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'isg.forest';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'p_no', 'o_aza', 'ko_aza', 'type', 'owner', 'o_addr', 'm_addr', 'manager', 'area', 'memo', 'o_type'], 'default', 'value' => null],
            [['geom'], 'string'],
            [['area'], 'number'],
            [['p_no'], 'string', 'max' => 25],
            [['o_aza', 'ko_aza', 'type'], 'string', 'max' => 10],
            [['owner'], 'string', 'max' => 22],
            [['o_addr'], 'string', 'max' => 65],
            [['m_addr'], 'string', 'max' => 71],
            [['manager'], 'string', 'max' => 30],
            [['memo'], 'string', 'max' => 64],
            [['o_type'], 'string', 'max' => 8],
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
            'p_no' => '番地',
            'o_aza' => '大字',
            'ko_aza' => '小字',
            'type' => '種類',
            'owner' => '所有者',
            'o_addr' => '所有者住所',
            'm_addr' => '管理者住所',
            'manager' => '管理者',
            'area' => '面積',
            'memo' => 'メモ',
            'o_type' => '所有区分',
        ];
    }

}

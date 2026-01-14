<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "forest".
 *
 * @property int $id
 * @property string $geom
 * @property string|null $p_no
 * @property int|null $aza_id
 * @property int|null $type_id
 * @property int|null $owner_id
 * @property int|null $manager_id
 * @property float|null $area
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Aza $aza
 * @property User $createdBy
 * @property Person $manager
 * @property Person $owner
 * @property Frtype $type
 * @property User $updatedBy
 */
class Forest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'forest';
    }

    private $_title = null;

    public function getTitle()
    {
        if ($this->_title === null) {
            if ($this->p_no != '') {
                $this->_title = trim($this->aza_name . ' ' . $this->p_no);
            } else {
                $this->_title = trim($this->aza_name . ' ID[' . $this->id . ']');
            }
        }
        return $this->_title;
    }

    private $_aza_name = null;

    public function getAza_Name()
    {
        if ($this->_aza_name === null) {
            if ($this->aza_id) {
                $this->_aza_name = $this->aza->name;
            } else {
                $this->_aza_name = '';
            }
        }
        return $this->_aza_name;
    }

    private $_type_name = null;

    public function getType_Name()
    {
        if ($this->_type_name === null) {
            if ($this->type_id) {
                $this->_type_name = $this->type->name;
            } else {
                $this->_type_name = '';
            }
        }
        return $this->_type_name;
    }

    private $_owner_name = null;

    public function getOwner_Name()
    {
        if ($this->_owner_name === null) {
            if ($this->owner_id) {
                $this->_owner_name = $this->owner->dispname;
            } else {
                $this->_owner_name = '';
            }
        }
        return $this->_owner_name;
    }

    private $_manager_name = null;

    public function getManager_Name()
    {
        if ($this->_manager_name === null) {
            if ($this->manager_id) {
                $this->_manager_name = $this->manager->dispname;
            } else {
                $this->_manager_name = '';
            }
        }
        return $this->_manager_name;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aza_id', 'type_id', 'owner_id', 'manager_id'], 'default', 'value' => null],
            [['note'], 'default', 'value' => ''],
            [['area'], 'default', 'value' => 0],
            [['updated_by'], 'default', 'value' => 1],
            [['geom'], 'required'],
            [['geom'], 'string'],
            [['aza_id', 'type_id', 'owner_id', 'manager_id', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['aza_id', 'type_id', 'owner_id', 'manager_id', 'created_by', 'updated_by'], 'integer'],
            [['area'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['p_no'], 'string', 'max' => 30],
            [['note'], 'string', 'max' => 80],
            [['aza_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aza::class, 'targetAttribute' => ['aza_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Frtype::class, 'targetAttribute' => ['type_id' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['manager_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
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
            'aza_id' => '字（あざ）',
            'type_id' => 'タイプ',
            'owner_id' => '所有者',
            'manager_id' => '管理者',
            'area' => '面積',
            'note' => 'メモ',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * Gets query for [[Aza]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAza()
    {
        return $this->hasOne(Aza::class, ['id' => 'aza_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Manager]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Person::class, ['id' => 'manager_id']);
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Person::class, ['id' => 'owner_id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Frtype::class, ['id' => 'type_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public const MAP_URL =
        'https://gis.isarigami.net/?t=isg-agfr&l=forest,p_no!,agri!,bld!,road,water,isarigami!,sh355!,sh35!,sh79~,sh125!,sh172!,ir355!,ir35!,ir79~,ir125!,ir172!,contour~,cs!,dem-shade!,dsm-shade!,dem!,dsm!&bl=g-sat';

    private $_map_url = null;

    public function getMapUrl()
    {
        if ($this->_map_url === null) {
            $sql = <<< SQL
SELECT
  public.ST_X(public.ST_Transform(public.ST_pointonsurface((geom)::public.geometry), 3857)) AS x,
  public.ST_Y(public.ST_Transform(public.ST_PointOnSurface((geom)::public.geometry), 3857)) AS y
FROM agri.forest
  WHERE id = :id
SQL;
            $sql2 = <<< SQL2
SELECT
  public.ST_XMin(e) AS xmin, public.ST_YMin(e) AS ymin,
  public.ST_XMax(e) AS xmax, public.ST_YMax(e) AS ymax
FROM (
  SELECT public.ST_Extent(public.ST_Expand(public.ST_Transform((geom)::public.geometry, 3857), 50)) AS e
  FROM agri.forest
  WHERE id = :id
)
SQL2;
            $pt = Yii::$app->db->createCommand($sql, ['id' => $this->id])->queryOne();
            $ex = Yii::$app->db->createCommand($sql2, ['id' => $this->id])->queryOne();
            $this->_map_url = self::MAP_URL
                . "&e={$ex['xmin']},{$ex['ymin']},{$ex['xmax']},{$ex['ymax']}"
                . "&c={$pt['x']},{$pt['y']}&hc=1";
        }
        return $this->_map_url;
    }
}

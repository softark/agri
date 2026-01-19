<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "field".
 *
 * @property int $id
 * @property string $geom
 * @property int|null $aza_id
 * @property string|null $p_no
 * @property float|null $c_area
 * @property float|null $f_area
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Aza $aza
 * @property FieldPerson $ownerFieldPerson
 * @property Person $owner
 * @property FieldPerson $cultivatorFieldPerson
 * @property Person $cultivator
 * @property FieldPerson[] $ownerFieldPersons
 * @property Person[] $owners
 * @property FieldPerson[] $cultivatorFieldPersons
 * @property Person[] $cultivators
 * @property FieldUsage[] $fieldUsages
 * @property Usage[] $usages
 * @property FieldUsage $fieldUsage
 * @property Usage $usage
 * @property User $createdBy
 * @property User $updatedBy
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    private ?string $_aza_name = null;

    public function getAza_Name(): string
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
    private ?string $_owner_name = null;

    public function getOwner_Name()
    {
        if ($this->_owner_name === null) {
            if ($this->owner) {
                $this->_owner_name = $this->owner->dispname;
            } else {
                $this->_owner_name = '';
            }
        }
        return $this->_owner_name;
    }

    private ?string $_cultivator_name = null;

    public function getCultivator_Name()
    {
        if ($this->_cultivator_name === null) {
            if ($this->cultivator) {
                $this->_cultivator_name = $this->cultivator->dispname;
            } else {
                $this->_cultivator_name = '';
            }
        }
        return $this->_cultivator_name;
    }

    private ?string $_usage_name = null;

    public function getUsage_Name()
    {
        if ($this->_usage_name === null) {
            if ($this->usage) {
                $this->_usage_name = $this->usage->name;
            } else {
                $this->_usage_name = '';
            }
        }
        return $this->_usage_name;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aza_id'], 'default', 'value' => null],
            [['note'], 'default', 'value' => ''],
            [['f_area'], 'default', 'value' => 0],
            [['updated_by'], 'default', 'value' => 1],
            [['geom'], 'required'],
            [['geom'], 'string'],
            [['aza_id', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['aza_id', 'created_by', 'updated_by'], 'integer'],
            [['c_area', 'f_area'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['p_no'], 'string', 'max' => 30],
            [['note'], 'string', 'max' => 80],
            [['aza_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aza::class, 'targetAttribute' => ['aza_id' => 'id']],
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
            'aza_id' => '字（あざ）',
            'p_no' => '番地',
            'owner' => '所有者',
            'cultivator' => '耕作者',
            'usage' => '農地利用状況',
            'c_area' => '計算面積',
            'f_area' => '面積',
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

    private $_owner_fps = null;

    public function getOwnerFieldPersons()
    {
        if ($this->_owner_fps === null) {
            $this->_owner_fps = FieldPerson::find()
                ->where('field_id = :id and role = :role')
                ->params([':id' => $this->id, ':role' => FieldPerson::ROLE_OWNER])
                ->orderBy('created_at ASC')
                ->all();
        }
        return $this->_owner_fps;
    }

    private $_owner_fp = false;
    public function getOwnerFieldPerson()
    {
        if ($this->_owner_fp === false) {
            $this->_owner_fp = FieldPerson::find()
                ->where('field_id = :id and role = :role and valid_to is null')
                ->params([':id' => $this->id, ':role' => FieldPerson::ROLE_OWNER])
                ->one();
        }
        return $this->_owner_fp;
    }

    public function getOwners()
    {
        $owners = [];
        foreach ($this->getOwnerFieldPersons() as $ofp) {
            $owners[] = $ofp->person;
        }
        return $owners;
    }

    public function getOwner()
    {
        return $this->getOwnerFieldPerson() ? $this->getOwnerFieldPerson()->person : null;
    }

    private $_owner_id = -1;
    public function getOwner_id()
    {
        if ($this->_owner_id == -1) {
            $this->_owner_id = $this->getOwner() ? $this->getOwner()->id : null;
        }
        return $this->_owner_id;
    }

    private $_cultivator_fps = null;

    public function getCultivatorFieldPersons()
    {
        if ($this->_cultivator_fps === null) {
            $this->_cultivator_fps = FieldPerson::find()
                ->where('field_id = :id and role = :role')
                ->params([':id' => $this->id, ':role' => FieldPerson::ROLE_CULTIVATOR])
                ->orderBy('created_at ASC')
                ->all();
        }
        return $this->_cultivator_fps;
    }

    private $_cultivator_fp = false;
    public function getCultivatorFieldPerson()
    {
        if ($this->_cultivator_fp === false) {
            $this->_cultivator_fp = FieldPerson::find()
                ->where('field_id = :id and role = :role and valid_to is null')
                ->params([':id' => $this->id, ':role' => FieldPerson::ROLE_CULTIVATOR])
                ->one();
        }
        return $this->_cultivator_fp;
    }

    public function getCultivators()
    {
        $cultivators = [];
        foreach ($this->getCultivatorFieldPersons() as $ofp) {
            $cultivators[] = $ofp->person;
        }
        return $cultivators;
    }

    public function getCultivator()
    {
        return $this->getCultivatorFieldPerson() ? $this->getCultivatorFieldPerson()->person : null;
    }

    private $_cultivator_id = -1;
    public function getCultivator_id()
    {
        if ($this->_cultivator_id == -1) {
            $this->_cultivator_id = $this->getCultivator() ? $this->getCultivator()->id : null;
        }
        return $this->_cultivator_id;
    }

    private $_fus = null;

    public function getFieldUsages()
    {
        if ($this->_fus === null) {
            $this->_fus = FieldUsage::find()
                ->where('field_id = :id')
                ->params([':id' => $this->id])
                ->orderBy('created_at ASC')
                ->all();
        }
        return $this->_fus;
    }

    private $_fu = false;
    public function getFieldUsage()
    {
        if ($this->_fu === false) {
            $this->_fu = FieldUsage::find()
                ->where('field_id = :id and valid_to is null')
                ->params([':id' => $this->id])
                ->one();
        }
        return $this->_fu;
    }

    public function getUsages()
    {
        $usages = [];
        foreach ($this->getFieldUsages() as $fu) {
            $usages[] = $fu->usage;
        }
        return $usages;
    }

    public function getUsage()
    {
        return $this->getFieldUsage() ? $this->getFieldUsage()->usage : null;
    }

    private $_usage_id = -1;
    public function getUsage_id()
    {
        if ($this->_usage_id == -1) {
            $this->_usage_id = $this->getUseage() ? $this->getUsage()->id : null;
        }
        return $this->_usage_id;
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public const MAP_URL =
        'https://gis.isarigami.net/?t=isg-agfr&l=forest!,p_no!,agri,bld,road,water,isarigami!,sh355!,sh35!,sh79~,sh125!,sh172!,ir355!,ir35!,ir79~,ir125!,ir172!,contour~,cs!,dem-shade!,dsm-shade!,dem!,dsm!&bl=g-sat';

    private ?string $_map_url = null;

    public function getMapUrl(): ?string
    {
        if ($this->_map_url === null) {
            $sql = <<< SQL
SELECT
  public.ST_X(public.ST_Transform(public.ST_pointonsurface((geom)::public.geometry), 3857)) AS x,
  public.ST_Y(public.ST_Transform(public.ST_PointOnSurface((geom)::public.geometry), 3857)) AS y
FROM agri.field
  WHERE id = :id
SQL;
            $sql2 = <<< SQL2
SELECT
  public.ST_XMin(e) AS xmin, public.ST_YMin(e) AS ymin,
  public.ST_XMax(e) AS xmax, public.ST_YMax(e) AS ymax
FROM (
  SELECT public.ST_Extent(public.ST_Expand(public.ST_Transform((geom)::public.geometry, 3857), 50)) AS e
  FROM agri.field
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

    /**
     * @param bool $insert
     * @return bool
     */
    public
    function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $user_id = (Yii::$app->user->isGuest) ? 1 : Yii::$app->user->id;
            if ($insert) {
                $this->created_by = $user_id;
            }
            $this->updated_by = $user_id;
            $dt = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
            $this->updated_at = $dt->format("Y-m-d H:i:s T");
            return true;
        } else {
            return false;
        }
    }
}

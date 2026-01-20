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
 * @property int|null $p_no_sort
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

    public static function getAreaText($area, $mode = 'a')
    {
        if ($mode == 'a') {
            if ($area < 10000.0) {
                return number_format($area / 100.0, 2) . ' a';
            } else {
                return number_format($area / 10000.0, 2) . ' ha';
            }
        } else {
            if ($area < 1000.0) {
                return number_format($area / 100.0, 2) . ' 畝';
            } else {
                return number_format($area / 1000.0, 2) . ' 反';
            }
        }
    }

    public static function getAreaTextFull($area)
    {
        return self::getAreaText($area, 'a') . ' / ' . self::getAreaText($area, 't') . ' / '. number_format($area, 0) . ' ㎡';
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
            'c_area' => '地図面積',
            'f_area' => '公称面積',
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

    public function getOwnerFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_OWNER])
            ->orderBy(['field_person.valid_from' => SORT_ASC]);
    }

    public function getOwnerFieldPerson()
    {
        return $this->hasOne(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_OWNER])
            ->andOnCondition(['field_person.valid_to' => null]);
    }

    private $_owners = null;

    public function getOwners()
    {
        if ($this->_owners === null) {
            $this->_owners = [];
            foreach ($this->ownerFieldPersons as $fp) {
                $this->_owners[] = $fp->person;
            }
        }
        return $this->_owners;
    }

    public function getOwner()
    {
        return $this->ownerFieldPerson ? $this->ownerFieldPerson->person : null;
    }

    private $_owner_id = -1;

    public function getOwner_id()
    {
        if ($this->_owner_id == -1) {
            $this->_owner_id = $this->owner ? $this->owner->id : null;
        }
        return $this->_owner_id;
    }

    public function getCultivatorFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_CULTIVATOR])
            ->orderBy(['field_person.valid_from' => SORT_ASC]);
    }

    public function getCultivatorFieldPerson()
    {
        return $this->hasOne(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_CULTIVATOR])
            ->andOnCondition(['field_person.valid_to' => null]);
    }

    private $_cultivators = null;

    public function getCultivators()
    {
        if ($this->_cultivators === null) {
            $this->_cultivators = [];
            foreach ($this->cultivatorFieldPersons as $fp) {
                $this->_cultivators[] = $fp->person;
            }
        }
        return $this->_cultivators;
    }

    public function getCultivator()
    {
        return $this->cultivatorFieldPerson ? $this->cultivatorFieldPerson->person : null;
    }

    private $_cultivator_id = -1;

    public function getCultivator_id()
    {
        if ($this->_cultivator_id == -1) {
            $this->_cultivator_id = $this->cultivator ? $this->cultivator->id : null;
        }
        return $this->_cultivator_id;
    }

    public function getFieldUsages()
    {
        return $this->hasMany(FieldUsage::class, ['field_id' => 'id'])
            ->orderBy(['field_usage.valid_from' => SORT_ASC]);
    }

    public function getFieldUsage()
    {
        return $this->hasOne(FieldUsage::class, ['field_id' => 'id'])
            ->andOnCondition(['field_usage.valid_to' => null]);
    }

    private $_usages = null;

    public function getUsages()
    {
        if ($this->_usages === null) {
            $this->_usages = [];
            foreach ($this->fieldUsages as $fu) {
                $this->_usages[] = $fu->usage;
            }
        }
        return $this->_usages;
    }

    public function getUsage()
    {
        return $this->fieldUsage ? $this->fieldUsage->usage : null;
    }

    private $_usage_id = -1;

    public function getUsage_id()
    {
        if ($this->_usage_id == -1) {
            $this->_usage_id = $this->useage ? $this->usage->id : null;
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

    public static function modifyFieldAreas()
    {
        $count = 0;
//        $models = Field::find()->all();
//        foreach ($models as $model) {
//            $model->f_area = $model->c_area;
//            $model->save();
//            $count++;
//        }
//        return $count;

        $path = Yii::getAlias('@app/migrations/data/sato_aza.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $p_no = ((int)$row[2]) * 1000;
            $p_no_next = $p_no + 1000;
            $area = (double)$row[3] * 100;

            $field = Field::find()
                ->where('p_no_sort >= :p_no and p_no_sort < :p_no_next',
                    [':p_no' => $p_no, ':p_no_next' => $p_no_next])
                ->one();
            if ($field) {
                $field->f_area = $area;
                $field->save();
                $count++;
            }
        }
        fclose($fp);
        return $count;
    }
}

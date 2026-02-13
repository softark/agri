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
 *
 * @property FieldPerson $ownerFieldPerson
 * @property Person $owner
 * @property FieldPerson $cultivatorFieldPerson
 * @property Person $cultivator
 * @property FieldPerson $chusankanFieldPerson
 * @property Person $chusankan
 * @property FieldPerson $saimokushoFieldPerson
 * @property Person $saimokusho
 *
 * @property FieldPerson[] $ownerFieldPersons
 * @property Person[] $owners
 * @property FieldPerson[] $cultivatorFieldPersons
 * @property Person[] $cultivators
 * @property FieldPerson[] $chusankanFieldPersons
 * @property Person[] $chusankans
 * @property FieldPerson[] $saimokushoFieldPersons
 * @property Person[] $saimokushos
 *
 * @property FieldUsage[] $fieldUsages
 * @property Usage[] $usages
 * @property FieldUsage $fieldUsage
 * @property Usage $usage
 * @property User $createdBy
 * @property User $updatedBy
 */
class Field extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'audit' => [
                'class' => AuditBehavior::class,
                'modelName' => 'agri.field', // 好み。未指定でも tableName() が入る
                // 'ignoreAttributes' => ['updated_at'], // 必要なら上書き
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    // 地物の BBOX
    private $_xmin;
    private $_ymin;
    private $_xmax;
    private $_ymax;
    
    public function getXmin()
    {
        if ($this->_xmin == null) {
            $this->getBBox();
        }
        return $this->_xmin;
    }
    public function setXmin($value)
    {
        $this->_xmin = $value;
    }

    public function getYmin()
    {
        if ($this->_ymin == null) {
            $this->getBBox();
        }
        return $this->_ymin;
    }
    public function setYmin($value)
    {
        $this->_ymin = $value;
    }

    public function getXmax()
    {
        if ($this->_xmax == null) {
            $this->getBBox();
        }
        return $this->_xmax;
    }
    public function setXmax($value)
    {
        $this->_xmax = $value;
    }

    public function getYmax()
    {
        if ($this->_ymax == null) {
            $this->getBBox();
        }
        return $this->_ymax;
    }
    public function setYmax($value)
    {
        $this->_ymax = $value;
    }

    private function getBBox()
    {
        $row = Field::find()->where(['id' => $this->id])
            ->select([
                'public.ST_XMin(bbox_3857) as xmin',
                'public.ST_YMin(bbox_3857) as ymin',
                'public.ST_XMax(bbox_3857) as xmax',
                'public.ST_YMax(bbox_3857) as ymax',
            ])->one();
        if ($row) {
            $this->_xmin = $row['xmin'];
            $this->_ymin = $row['ymin'];
            $this->_xmax = $row['xmax'];
            $this->_ymax = $row['ymax'];
        } else {
            $this->_xmin = 0;
            $this->_ymin = 0;
            $this->_xmax = 0;
            $this->_ymax = 0;
        }
    }

    // 地物の中心点
    private $_cx;
    private $_cy;

    public function getCx()
    {
        if ($this->_cx == null) {
            $this->getCenterPoint();
        }
        return $this->_cx;
    }
    public function setCx($value)
    {
        $this->_cx = $value;
    }

    public function getCy()
    {
        if ($this->_cy == null) {
            $this->getCenterPoint();
        }
        return $this->_cy;
    }
    public function setCy($value)
    {
        $this->_cy = $value;
    }

    private function getCenterPoint()
    {
        $row = Field::find()->where(['id' => $this->id])
            ->select([
                'public.ST_X(center_3857) as cx',
                'public.ST_Y(center_3857) as cy',
            ])->one();
        if ($row) {
            $this->_cx = $row['cx'];
            $this->_cy = $row['cy'];
        } else {
            $this->_cx = 0;
            $this->_cy = 0;
        }
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

    private $_p_str = null;

    public function getP_str(): string
    {
        if ($this->_p_str === null) {
            $strs = preg_split('/\//', $this->p_no);
            if (count($strs) < 2) {
                $this->_p_str = $this->p_no;
            } else {
                $this->_p_str = $strs[0] . ' 他';
            }
        }
        return $this->_p_str;
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

    private ?string $_chusankan_name = null;

    public function getChusankan_Name()
    {
        if ($this->_chusankan_name === null) {
            if ($this->chusankan) {
                $this->_chusankan_name = $this->chusankan->dispname;
            } else {
                $this->_chusankan_name = '';
            }
        }
        return $this->_chusankan_name;
    }

    private ?string $_saimokusho_name = null;

    public function getSaimokusho_Name()
    {
        if ($this->_saimokusho_name === null) {
            if ($this->saimokusho) {
                $this->_saimokusho_name = $this->saimokusho->dispname;
            } else {
                $this->_saimokusho_name = '';
            }
        }
        return $this->_saimokusho_name;
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
        return self::getAreaText($area, 'a') . ' / ' . self::getAreaText($area, 't') . ' / ' . number_format($area, 0) . ' ㎡';
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
            'chusankan' => '中山間',
            'saimokusho' => '細目書',
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

    public function getChusankanFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_CHUSANKAN])
            ->orderBy(['field_person.valid_from' => SORT_ASC]);
    }

    public function getChusankanFieldPerson()
    {
        return $this->hasOne(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_CHUSANKAN])
            ->andOnCondition(['field_person.valid_to' => null]);
    }

    private $_chusankans = null;

    public function getChusankans()
    {
        if ($this->_chusankans === null) {
            $this->_chusankans = [];
            foreach ($this->chusankanFieldPersons as $fp) {
                $this->_chusankans[] = $fp->person;
            }
        }
        return $this->_chusankans;
    }

    public function getChusankan()
    {
        return $this->chusankanFieldPerson ? $this->chusankanFieldPerson->person : null;
    }

    private $_chusankan_id = -1;

    public function getChusankan_id()
    {
        if ($this->_chusankan_id == -1) {
            $this->_chusankan_id = $this->chusankan ? $this->chusankan->id : null;
        }
        return $this->_chusankan_id;
    }

    public function getSaimokushoFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_SAIMOKUSHO])
            ->orderBy(['field_person.valid_from' => SORT_ASC]);
    }

    public function getSaimokushoFieldPerson()
    {
        return $this->hasOne(FieldPerson::class, ['field_id' => 'id'])
            ->andOnCondition(['field_person.role' => FieldPerson::ROLE_SAIMOKUSHO])
            ->andOnCondition(['field_person.valid_to' => null]);
    }

    private $_saimokushos = null;

    public function getSaimokushos()
    {
        if ($this->_saimokushos === null) {
            $this->_saimokushos = [];
            foreach ($this->saimokushoFieldPersons as $fp) {
                $this->_saimokushos[] = $fp->person;
            }
        }
        return $this->_saimokushos;
    }

    public function getSaimokusho()
    {
        return $this->saimokushoFieldPerson ? $this->saimokushoFieldPerson->person : null;
    }

    private $_saimokusho_id = -1;

    public function getSaimokusho_id()
    {
        if ($this->_saimokusho_id == -1) {
            $this->_saimokusho_id = $this->saimokusho ? $this->saimokusho->id : null;
        }
        return $this->_saimokusho_id;
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

    public const MAP_SERVER = YII_ENV_DEV ? 'https://gis.vmware/' : 'https://gis.isarigami.net/';
    public const MAP_URL = self::MAP_SERVER . '?t=isg-agfr&l=forest!,f_forest!,p_no!,agri,f_agri,bld,road,water,isarigami!,sh355!,sh35!,sh79~,sh125!,sh172!,ir355!,ir35!,ir79~,ir125!,ir172!,contour~,cs!,dem-shade!,dsm-shade!,dem!,dsm!&bl=g-sat';

    private ?string $_map_url = null;

    public function getMapUrl(): ?string
    {
        if ($this->_map_url === null) {
            $filter = [
                "__custom" => [[
                    "title" => "選択された農地",
                    "layer" => "f_agri",
                    "expr" => ["id", "=", (int)$this->id],   // まずは1件ならこれ
                ]]
            ];
            $f = rawurlencode(json_encode($filter, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $ext_xmin = $this->xmin - 50;
            $ext_ymin = $this->ymin - 50;
            $ext_xmax = $this->xmax + 50;
            $ext_ymax = $this->ymax + 50;

            $this->_map_url = self::MAP_URL
                . "&e=$ext_xmin,$ext_ymin,$ext_xmax,$ext_ymax"
                // . "&c=$this->cx,$this->cy"&hc=1"
                . "&c=$this->cx,$this->cy"
                . "&f=$f";
        }
        return $this->_map_url;
    }

    public static function getSelectionMapUrl($ids, $bbox): string
    {
        $xmin = $bbox['xmin'] - 100;
        $ymin = $bbox['ymin'] - 100;
        $xmax = $bbox['xmax'] + 100;
        $ymax = $bbox['ymax'] + 100;

        $ptx = ($bbox['xmin'] + $bbox['xmax']) / 2;
        $pty = ($bbox['ymin'] + $bbox['ymax']) / 2;

        $filter = [
            "__custom" => [[
                "title" => "選択された農地",
                "layer" => "f_agri",
                "expr" => ["id", "in", $ids],
            ]]
        ];
        $f = rawurlencode(json_encode($filter, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::MAP_URL
            . "&e=$xmin,$ymin,$xmax,$ymax"
            // . "&c=$ptx,$pty&hc=1"
            . "&c=$ptx,$pty"
            . "&f=$f";
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
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

    /**
     * @param $fp FieldPerson
     * @return void
     */
    public static function deleteFieldPerson($fp)
    {
        $field_id = $fp->field_id;
        $role = $fp->role;
        $valid_from = $fp->valid_from;

        $prev = FieldPerson::find()
            ->where(['and',
                ['field_id' => $field_id],
                ['role' => $role],
                ['<', 'valid_from', $valid_from]
            ])->orderBy(['valid_from' => SORT_DESC])->one();

        $next = FieldPerson::find()
            ->where(['and',
                ['field_id' => $field_id],
                ['role' => $role],
                ['>', 'valid_from', $valid_from]
            ])->orderBy(['valid_from' => SORT_ASC])->one();

        $tr = Yii::$app->db->beginTransaction();
        try {
            $fp->delete();
            if ($prev && $next) {
                $prev->valid_to = $valid_from;
                $prev->save();
            } else if ($prev) {
                $prev->valid_to = null;
                $prev->save();
            } else if ($next) {
                $next->valid_from = '1900-01-01';
                $next->save();
            }
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }
    }

    /**
     * @param $fu FieldUsage
     * @return void
     */
    public static function deleteFieldUsage($fu)
    {
        $field_id = $fu->field_id;
        $valid_from = $fu->valid_from;

        $prev = FieldUsage::find()
            ->where(['and',
                ['field_id' => $field_id],
                ['<', 'valid_from', $valid_from]
            ])->orderBy(['valid_from' => SORT_DESC])->one();

        $next = FieldUsage::find()
            ->where(['and',
                ['field_id' => $field_id],
                ['>', 'valid_from', $valid_from]
            ])->orderBy(['valid_from' => SORT_ASC])->one();

        $tr = Yii::$app->db->beginTransaction();
        try {
            $fu->delete();
            if ($prev && $next) {
                $prev->valid_to = $valid_from;
                $prev->save();
            } else if ($prev) {
                $prev->valid_to = null;
                $prev->save();
            } else if ($next) {
                $next->valid_from = '1900-01-01';
                $next->save();
            }
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }
    }
}

<?php

namespace app\models;

use Yii;
use yii\base\UserException;
use yii\db\Query;

/**
 * This is the model class for table "forest".
 *
 * @property int $id
 * @property string $geom
 * @property string|null $p_no
 * @property int|null $p_no_sort
 * @property int|null $aza_id
 * @property int|null $type_id
 * @property float|null $area
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Aza $aza
 * @property ForestPerson $ownerForestPerson
 * @property Person $owner
 * @property ForestPerson $managerForestPerson
 * @property Person $manager
 * @property ForestPerson[] $ownerForestPersons
 * @property Person[] $owners
 * @property ForestPerson[] $managerForestPersons
 * @property Person[] $managers
 * @property Frtype $type
 * @property User $createdBy
 * @property User $updatedBy
 */
class Forest extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'audit' => [
                'class' => AuditBehavior::class,
                'modelName' => 'agri.forest', // 好み。未指定でも tableName() が入る
                // 'ignoreAttributes' => ['updated_at'], // 必要なら上書き
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'forest';
    }

    private ?string $_title = null;

    public function getTitle(): string
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

    private ?string $_type_name = null;

    private $_p_str = null;
    public function getP_str(): string
    {
        if ($this->_p_str === null) {
            if ($this->p_no != '') {
                $this->_p_str = $this->p_no;
            } else {
                $this->_p_str = '(不詳)';
            }
        }
        return $this->_p_str;
    }

    public function getType_Name(): string
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

    private ?string $_manager_name = null;

    public function getManager_Name()
    {
        if ($this->_manager_name === null) {
            if ($this->manager) {
                $this->_manager_name = $this->manager->dispname;
            } else {
                $this->_manager_name = '';
            }
        }
        return $this->_manager_name;
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
            } elseif ($area < 10000.0) {
                return number_format($area / 1000.0, 2) . ' 反';
            } else {
                return number_format($area / 10000.0, 2) . ' 町';
            }
        }
    }

    public static function getAreaTextFull($area, $mode = 'a')
    {
        return self::getAreaTextLong($area, $mode) . ' / ' . number_format($area, 0) . ' ㎡';
    }

    public static function getAreaTextLong($area)
    {
        return self::getAreaText($area, 'a') . ' / ' . self::getAreaText($area, 't');
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['aza_id', 'type_id'], 'default', 'value' => null],
            [['note'], 'default', 'value' => ''],
            [['area'], 'default', 'value' => 0],
            [['updated_by'], 'default', 'value' => 1],
            [['geom'], 'required'],
            [['geom'], 'string'],
            [['aza_id', 'type_id', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['aza_id', 'type_id', 'created_by', 'updated_by'], 'integer'],
            [['area'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['p_no'], 'string', 'max' => 30],
            [['note'], 'string', 'max' => 80],
            [['aza_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aza::class, 'targetAttribute' => ['aza_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Frtype::class, 'targetAttribute' => ['type_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'geom' => 'Geom',
            'p_no' => '番地',
            'aza_id' => '字（あざ）',
            'type_id' => 'タイプ',
            'owner' => '所有者',
            'manager' => '管理者',
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
    public function getAza(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Aza::class, ['id' => 'aza_id']);
    }

    public function getOwnerForestPersons()
    {
        return $this->hasMany(ForestPerson::class, ['forest_id' => 'id'])
            ->andOnCondition(['forest_person.role' => ForestPerson::ROLE_OWNER])
            ->orderBy(['forest_person.valid_from' => SORT_ASC]);
    }

    public function getOwnerForestPerson()
    {
        return $this->hasOne(ForestPerson::class, ['forest_id' => 'id'])
            ->andOnCondition(['forest_person.role' => ForestPerson::ROLE_OWNER])
            ->andOnCondition(['forest_person.valid_to' => null]);
    }

    private $_owners = null;

    public function getOwners()
    {
        if ($this->_owners === null) {
            $this->_owners = [];
            foreach ($this->ownerForestPersons as $fp) {
                $this->_owners[] = $fp->person;
            }
        }
        return $this->_owners;
    }

    public function getOwner()
    {
        return $this->ownerForestPerson ? $this->ownerForestPerson->person : null;
    }

    private $_owner_id = -1;

    public function getOwner_id()
    {
        if ($this->_owner_id == -1) {
            $this->_owner_id = $this->owner ? $this->owner->id : null;
        }
        return $this->_owner_id;
    }

    public function getManagerForestPersons()
    {
        return $this->hasMany(ForestPerson::class, ['forest_id' => 'id'])
            ->andOnCondition(['forest_person.role' => ForestPerson::ROLE_MANAGER])
            ->orderBy(['forest_person.valid_from' => SORT_ASC]);
    }

    public function getManagerForestPerson()
    {
        return $this->hasOne(ForestPerson::class, ['forest_id' => 'id'])
            ->andOnCondition(['forest_person.role' => ForestPerson::ROLE_MANAGER])
            ->andOnCondition(['forest_person.valid_to' => null]);
    }

    private $_managers = null;

    public function getManagers()
    {
        if ($this->_managers === null) {
            $this->_managers = [];
            foreach ($this->managerForestPersons as $fp) {
                $this->_managers[] = $fp->person;
            }
        }
        return $this->_managers;
    }

    public function getManager()
    {
        return $this->managerForestPerson ? $this->managerForestPerson->person : null;
    }

    private $_manager_id = -1;

    public function getManager_id()
    {
        if ($this->_manager_id == -1) {
            $this->_manager_id = $this->manager ? $this->manager->id : null;
        }
        return $this->_manager_id;
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Frtype::class, ['id' => 'type_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public const MAP_URL =
        'https://gis.isarigami.net/?t=isg-agfr&l=forest,p_no!,agri!,bld!,road,water,isarigami!,sh355!,sh35!,sh79~,sh125!,sh172!,ir355!,ir35!,ir79~,ir125!,ir172!,contour~,cs!,dem-shade!,dsm-shade!,dem!,dsm!&bl=g-sat';

    private ?string $_map_url = null;

    public function getMapUrl(): ?string
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

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
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

    /**
     * @param $fp ForestPerson
     * @return void
     */
    public static function deleteForestPerson($fp)
    {
        $forest_id = $fp->forest_id;
        $role = $fp->role;
        $valid_from = $fp->valid_from;

        $prev = ForestPerson::find()
            ->where(['and',
                ['forest_id' => $forest_id],
                ['role' => $role],
                ['<', 'valid_from', $valid_from]
            ])->orderBy(['valid_from' => SORT_DESC])->one();

        $next = ForestPerson::find()
            ->where(['and',
                ['forest_id' => $forest_id],
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
        }
        catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }
    }

}

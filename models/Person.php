<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property int $id
 * @property int $status
 * @property string $name1
 * @property string|null $name2
 * @property string|null $name
 * @property string|null $yomi1
 * @property string|null $yomi2
 * @property string|null $yomi
 * @property int $type
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property PersonRelation[] fromPersonRelations
 * @property PersonRelation[] toPersonRelations
 * @property Person[] fromPersons
 * @property Person[] toPersons
 * @property Person[] ancestors
 * @property Person[] descendants
 * @property Contact[] $contacts
 * @property Contact $contact
 * @property FieldPerson[] $fieldPersons
 * @property Field[] $fields
 * @property ForestPerson[] $forestPersons
 * @property Forest[] $forests
 * @property User $createdBy
 * @property User $updatedBy
 */
class Person extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'audit' => [
                'class' => AuditBehavior::class,
                'modelName' => 'agri.person', // 好み。未指定でも tableName() が入る
                // 'ignoreAttributes' => ['updated_at'], // 必要なら上書き
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person';
    }

    const TYPE_UNDEF = 0;
    const TYPE_INDIVIDUAL = 1;
    const TYPE_VOL_ORG = 2;
    const TYPE_CORPORATE = 3;
    const TYPE_GOVERNMENT = 4;

    public static function getTypes()
    {
        return [
            self::TYPE_UNDEF => '不詳',
            self::TYPE_INDIVIDUAL => '個人',
            self::TYPE_VOL_ORG => '任意団体',
            self::TYPE_CORPORATE => '法人',
            self::TYPE_GOVERNMENT => '国・地方自治体',
        ];
    }

    const STATUS_VALID = 1;
    const STATUS_INVALID = 0;

    public static function getStates()
    {
        return [
            self::STATUS_VALID => '有効',
            self::STATUS_INVALID => '無効',
        ];
    }

    public function getTypeText()
    {
        return self::getTypes()[$this->type];
    }

    public function getStatusText()
    {
        return self::getStates()[$this->status];
    }

    private $_dispname = null;

    public function getDispName()
    {
        if ($this->_dispname === null) {
            $this->_dispname = trim($this->name1 . ' ' . $this->name2);
        }
        return $this->_dispname;
    }

    private $_yomigana = null;

    public function getYomigana()
    {
        if ($this->_yomigana === null) {
            $this->_yomigana = trim($this->yomi1 . ' ' . $this->yomi2);
        }
        return $this->_yomigana;
    }

    private $_fullname = null;

    public function getFullName()
    {
        if ($this->_fullname === null) {
            if (count($this->descendants) > 0) {
                $dispnames = [];
                foreach ($this->descendants as $person) {
                    $dispnames[] = $person->getDispName();
                }
                $this->_fullname = $this->dispname . ' > ' . implode(', ', $dispnames);
            } else {
                $dispname = $this->getDispName();
                if (count($this->contacts) > 0) {
                    $contact = $this->contacts[0];
                    if ($contact->role != '' || ($contact->name != '' && $contact->name != $this->name)) {
                        $dispname .= ' : ' . $contact->fullname;
                    }
                }
                $this->_fullname = $dispname;
            }
        }
        return $this->_fullname;
    }

    /**
     * @return int[] 子孫 person_id の配列（重複なし）
     */
    public function getDescendantsIds(): array
    {
        if ($this->id === null) {
            return [];
        }

        $sql = <<<SQL
WITH RECURSIVE descendants AS (
    SELECT
        pr.to_person_id,
        ARRAY[pr.from_person_id, pr.to_person_id] AS path
    FROM person_relation pr
    WHERE pr.from_person_id = :person_id

    UNION ALL

    SELECT
        pr.to_person_id,
        d.path || pr.to_person_id
    FROM person_relation pr
    JOIN descendants d
      ON pr.from_person_id = d.to_person_id
    WHERE NOT pr.to_person_id = ANY(d.path)
)
SELECT DISTINCT to_person_id
FROM descendants
SQL;

        $rows = Yii::$app->db->createCommand($sql, [':person_id' => (int)$this->id])->queryColumn();

        // queryColumn() は文字列で返ることがあるので int に寄せる
        return array_map('intval', $rows);
    }

    private $_descendants = null;

    /**
     * @return Person[]
     */
    public function getDescendants(): array
    {
        if ($this->_descendants === null) {
            $ids = $this->getDescendantsIds();
            $this->_descendants = empty($ids) ? [] : Person::find()->where(['id' => $ids])->all();
        }
        return $this->_descendants;
    }

    /**
     * @return int[] 子孫 person_id の配列（重複なし）
     */
    public function getAncestorsIds(): array
    {
        if ($this->id === null) {
            return [];
        }

        $sql = <<<SQL
WITH RECURSIVE ancestors AS (
    SELECT
        pr.from_person_id,
        ARRAY[pr.to_person_id, pr.from_person_id] AS path
    FROM person_relation pr
    WHERE pr.to_person_id = :person_id

    UNION ALL

    SELECT
        pr.from_person_id,
        d.path || pr.from_person_id
    FROM person_relation pr
    JOIN ancestors d
      ON pr.to_person_id = d.from_person_id
    WHERE NOT pr.from_person_id = ANY(d.path)
)
SELECT DISTINCT from_person_id
FROM ancestors
SQL;

        $rows = Yii::$app->db->createCommand($sql, [':person_id' => (int)$this->id])->queryColumn();

        // queryColumn() は文字列で返ることがあるので int に寄せる
        return array_map('intval', $rows);
    }

    private $_ancestors = null;

    /**
     * @return Person[]
     */
    public function getAncestors(): array
    {
        if ($this->_ancestors === null) {
            $ids = $this->getAncestorsIds();
            $this->_ancestors = empty($ids) ? [] : Person::find()->where(['id' => $ids])->all();
        }
        return $this->_ancestors;
    }

    private $_field_ids = null;

    public function getField_ids()
    {
        if ($this->_field_ids === null) {
            $field_ids = [];
            foreach ($this->fieldPersons as $fp) {
                $field_ids[] = $fp->field_id;
            }
            $this->_field_ids = array_unique($field_ids);
        }
        return $this->_field_ids;
    }

    private $_forest_ids = null;

    public function getForest_ids()
    {
        if ($this->_forest_ids === null) {
            $forest_ids = [];
            foreach ($this->forestPersons as $fp) {
                $forest_ids[] = $fp->forest_id;
            }
            $this->_forest_ids = array_unique($forest_ids);
        }
        return $this->_forest_ids;
    }

    private $_num_fields = null;

    public function getNum_fields()
    {
        if ($this->_num_fields === null) {
            $this->_num_fields = count($this->field_ids);
        }
        return $this->_num_fields;
    }

    public function setNum_fields($num_fields): void
    {
        $this->_num_fields = $num_fields;
    }

    private $_num_forests = null;

    public function getNum_forests()
    {
        if ($this->_num_forests === null) {
            $this->_num_forests = count($this->forest_ids);
        }
        return $this->_num_forests;
    }

    public function setNum_forests($num_forests): void
    {
        $this->_num_forests = $num_forests;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => ''],
            [['status'], 'default', 'value' => self::STATUS_VALID],
            [['status'], 'in', 'range' => array_keys(self::getStates())],
            [['type'], 'default', 'value' => self::TYPE_INDIVIDUAL],
            [['type'], 'in', 'range' => array_keys(self::getTypes())],
            [['updated_by'], 'default', 'value' => 1],
            [['status', 'type', 'name1'], 'required'],
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [['status', 'type', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name1', 'name2', 'yomi1', 'yomi2'], 'string', 'max' => 30],
            [['name1', 'name2'], function ($attribute, $param, $validator) {
                $persons = Person::findAll(['name' => $this->name1 . $this->name2]);
                if (($this->isNewRecord && count($persons) > 0) || count($persons) > 1) {
                    $this->addError('name1', '姓 および 名の "' . $this->name1 . '"-"'
                        . $this->name2 . '" という組み合わせは既に登録されています。');
                }
            }],
            [['name', 'yomi'], 'string', 'max' => 60],
            [['note'], 'string', 'max' => 50],
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
            'status' => '状態',
            'type' => 'タイプ',
            'name1' => '氏／名称前半',
            'name2' => '名／名称後半',
            'name' => '氏名／名称',
            'dispname' => '氏名／名称',
            'yomi1' => 'よみがな（氏／名称前半）',
            'yomi2' => 'よみがな（名／名称後半）',
            'yomi' => 'よみがな',
            'yomigana' => 'よみがな',
            'note' => 'メモ',
            'contacts' => '連絡先',
            'contact' => '連絡先',
            'num_fields' => '農地',
            'num_forests' => '山林',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * Gets query for [[Contacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['person_id' => 'id'])->orderBy('order');
    }

    public function getContact()
    {
        if (count($this->contacts) > 0) {
            return $this->contacts[0];
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        if (count($this->contacts) > 0) {
            return $this->contacts[0]->getShortAddress();
        } else {
            return '';
        }
    }

    /**
     * Gets query for [[fromPersonRelations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromPersonRelations()
    {
        return $this->hasMany(PersonRelation::class, ['from_person_id' => 'id']);
    }

    /**
     * Gets query for [[fromPersons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromPersons()
    {
        return $this->hasMany(Person::class, ['id' => 'from_person_id'])
            ->viaTable('person_relation', ['to_person_id' => 'id']);
    }

    /**
     * Gets query for [[foPersonRelations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToPersonRelations()
    {
        return $this->hasMany(PersonRelation::class, ['to_person_id' => 'id']);
    }

    /**
     * Gets query for [[toPersons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToPersons()
    {
        return $this->hasMany(Person::class, ['id' => 'to_person_id'])
            ->viaTable('person_relation', ['from_person_id' => 'id']);
    }

    /**
     * Gets query for [[toPerson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'to_person_id']);
    }

    // 関連する農地
    
    public function getOwnerFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['person_id' => 'id'])
            ->alias('ofp')
            ->andOnCondition(['ofp.role' => FieldPerson::ROLE_OWNER]);
    }

    public function getCultivatorFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['person_id' => 'id'])
            ->alias('cfp')
            ->andOnCondition(['cfp.role' => FieldPerson::ROLE_CULTIVATOR]);
    }

    public function getOwnerFields()
    {
        return $this->hasMany(Field::class, ['id' => 'field_id'])
            ->via('ownerFieldPersons');
    }

    public function getCultivatorFields()
    {
        return $this->hasMany(Field::class, ['id' => 'field_id'])
            ->via('cultivatorFieldPersons');
    }

    public function getFieldPersons()
    {
        return $this->hasMany(FieldPerson::class, ['person_id' => 'id'])
            ->andOnCondition(['in', 'role', [
                FieldPerson::ROLE_OWNER,
                FieldPerson::ROLE_CULTIVATOR,
            ]]);
    }

    public function getFields()
    {
        return $this->hasMany(Field::class, ['id' => 'field_id'])
            ->via('fieldPersons')
            ->distinct();
    }

    // 関連する山林

    public function getOwnerForestPersons()
    {
        return $this->hasMany(ForestPerson::class, ['person_id' => 'id'])
            ->alias('ofp')
            ->andOnCondition(['ofp.role' => ForestPerson::ROLE_OWNER]);
    }

    public function getManagerForestPersons()
    {
        return $this->hasMany(ForestPerson::class, ['person_id' => 'id'])
            ->alias('mfp')
            ->andOnCondition(['cfp.role' => ForestPerson::ROLE_MANAGER]);
    }

    public function getOwnerForests()
    {
        return $this->hasMany(Forest::class, ['id' => 'Forest_id'])
            ->via('ownerForestPersons');
    }

    public function getManagerForests()
    {
        return $this->hasMany(Forest::class, ['id' => 'Forest_id'])
            ->via('managerForestPersons');
    }

    public function getForestPersons()
    {
        return $this->hasMany(ForestPerson::class, ['person_id' => 'id'])
            ->andOnCondition(['in', 'role', [
                ForestPerson::ROLE_OWNER,
                ForestPerson::ROLE_MANAGER,
            ]]);
    }

    public function getForests()
    {
        return $this->hasMany(Forest::class, ['id' => 'Forest_id'])
            ->via('ForestPersons')
            ->distinct();
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
}

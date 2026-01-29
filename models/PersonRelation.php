<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "person_relation".
 *
 * @property int $from_person_id
 * @property int $to_person_id
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Person $fromPerson
 * @property Person $toPerson
 * @property User $cratedBy
 * @property User $updatedBy
 */
class PersonRelation extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'audit' => [
                'class' => AuditBehavior::class,
                'modelName' => 'agri.person_relation', // 好み。未指定でも tableName() が入る
                // 'ignoreAttributes' => ['updated_at'], // 必要なら上書き
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => ''],
            [['updated_by'], 'default', 'value' => 1],
            [['from_person_id', 'to_person_id'], 'required'],
            [['from_person_id', 'to_person_id', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['from_person_id', 'to_person_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['note'], 'string', 'max' => 50],
            [['from_person_id', 'to_person_id'], 'unique', 'targetAttribute' => ['from_person_id', 'to_person_id']],
            [['from_person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['from_person_id' => 'id']],
            [['to_person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['to_person_id' => 'id']],
            ['from_person_id', 'compare', 'operator' => '!=', 'compareAttribute' => 'to_person_id'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'from_person_id' => '引継元',
            'to_person_id' => '引継先',
            'note' => 'メモ',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * Gets query for [[FromPerson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'from_person_id']);
    }

    /**
     * Gets query for [[ToPerson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'to_person_id']);
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
            if (!$insert) {
                $curModel = PersonRelation::findOne($this->id);
                $count = PersonRelation::find()->where(['from_person_id' => $curModel->from_person_id])->count();
                if ($count == 1) {
                    $curModel->fromPerson->status = Person::STATUS_VALID;
                    $curModel->fromPerson->save();
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->fromPerson->status = Person::STATUS_INVALID;
        $this->fromPerson->save();
    }

    public function beforeDelete()
    {
        $count = PersonRelation::find()->where(['from_person_id' => $this->from_person_id])->count();
        if ($count == 1) {
            $this->fromPerson->status = Person::STATUS_VALID;
            $this->fromPerson->save();
        }
        return parent::beforeDelete();
    }
}

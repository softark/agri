<?php

namespace app\models;

use Yii;
use yii\base\UserException;

/**
 * This is the model class for table "field_usage".
 *
 * @property int $id
 * @property int $field_id
 * @property int $usage_id
 * @property string $valid_from
 * @property string|null $valid_to
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property Field $field
 * @property User $updatedBy
 * @property Usage $usage
 */
class FieldUsage extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'audit' => [
                'class' => AuditBehavior::class,
                'modelName' => 'agri.field_usage', // 好み。未指定でも tableName() が入る
                // 'ignoreAttributes' => ['updated_at'], // 必要なら上書き
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['valid_to'], 'default', 'value' => null],
            [['updated_by'], 'default', 'value' => 1],
            [['note'], 'default', 'value' => ''],
            [['field_id', 'usage_id', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['field_id', 'usage_id', 'created_by', 'updated_by'], 'integer'],
            [['field_id', 'usage_id', 'valid_from'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['valid_from', 'valid_to'], 'date', 'format' => 'yyyy-MM-dd'],
            [['note'], 'string', 'max' => 80],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::class, 'targetAttribute' => ['field_id' => 'id']],
            [['usage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usage::class, 'targetAttribute' => ['usage_id' => 'id']],
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
            'field_id' => '農地',
            'usage_id' => '農地利用状況',
            'valid_from' => 'FROM',
            'valid_from_text' => 'FROM',
            'valid_to' => 'TO',
            'valid_to_text' => 'TO',
            'note' => 'メモ',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    public function getValid_from_text()
    {
        if ($this->valid_from == '1900-01-01') {
            return '****';
        } else {
            return $this->valid_from;
        }
    }

    public function getValid_to_text()
    {
        if ($this->valid_to == '') {
            return '現在';
        } else {
            return $this->valid_to;
        }
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
     * Gets query for [[Field]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::class, ['id' => 'field_id']);
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
     * Gets query for [[Usage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsage()
    {
        return $this->hasOne(Usage::class, ['id' => 'usage_id']);
    }

    public function addHistory()
    {
        $latest = FieldUsage::find()
            ->where(['field_id' => $this->field_id, 'valid_to' => null])
            ->one();
        $transaction = yii::$app->db->beginTransaction();
        try {
            if ($latest) {
                $latest->valid_to = $this->valid_from;
                if (!$latest->save()) {
                    Yii::error(['addHistory_failed_1', $latest->errors], __METHOD__);
                    throw new UserException("Failed to update the latest history.\n" . print_r($latest->errors, true));
                }
            }
            if (!$this->save()) {
                Yii::error(['addHistory_failed_2', $this->errors], __METHOD__);
                throw new UserException("Failed to add the new history.\n" . print_r($this->errors, true));
            }
            $transaction->commit();
        }
        catch (UserException $e) {
            $transaction->rollBack();
            throw $e;
        }
        return true;
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

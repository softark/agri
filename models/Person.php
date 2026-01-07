<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property int $id
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
 * @property Contact[] $contacts
 * @property Contact $priorContact
 * @property User $createdBy
 * @property User $updatedBy
 */
class Person extends \yii\db\ActiveRecord
{
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

    public static function getTypes() {
        return [
            self::TYPE_UNDEF => '不詳',
            self::TYPE_INDIVIDUAL => '個人',
            self::TYPE_VOL_ORG => '任意団体',
            self::TYPE_CORPORATE => '法人',
            self::TYPE_GOVERNMENT => '国・地方自治体',
        ];
    }

    public function getTypeText() {
        return self::getTypes()[$this->type];
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => ''],
            [['type'], 'default', 'value' => 0],
            [['updated_by'], 'default', 'value' => 1],
            [['name1'], 'required'],
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [['type', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name1', 'name2', 'yomi1', 'yomi2'], 'string', 'max' => 30],
            [['name1', 'name2'], function($attribute, $param, $validator) {
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
            'name1' => '姓（名前前半）',
            'name2' => '名（名前後半）',
            'name' => '名前',
            'dispname' => '名前',
            'yomi1' => 'よみがな（姓）',
            'yomi2' => 'よみがな（名）',
            'yomi' => 'よみがな',
            'yomigana' => 'よみがな',
            'type' => 'タイプ',
            'note' => 'メモ',
            'contacts' => '連絡先',
            'priorContact' => '連絡先',
            'priorAddress' => '住所',
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

    /**
     * @return Contact|null
     */
    public function getPriorContact()
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
    public function getPriorAddress()
    {
        if (count($this->contacts) > 0) {
            return $this->contacts[0]->getShortAddress();
        } else {
            return '';
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

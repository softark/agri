<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property string|null $zip
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $phone1
 * @property string|null $phone2
 * @property string|null $mail
 * @property string|null $note
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property PersonContact[] $personContacts
 * @property Person[] $persons
 * @property User $createdBy
 * @property User $updatedBy
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact';
    }

    public function getAddress()
    {
        return $this->address1 . $this->address2;
    }

    private $_shortaddress = null;
    public function getShortAddress()
    {
        if ($this->_shortaddress === null) {
            if (($pos = strpos($this->address, '岩座神')) !== false) {
                $this->_shortaddress = substr($this->address, $pos);
            } else if (($pos = strpos($this->address, '多可町')) !== false) {
                $this->_shortaddress = substr($this->address, $pos);
            } else if (($pos = strpos($this->address, '兵庫県')) !== false) {
                $this->_shortaddress = substr($this->address, $pos + strlen('兵庫県'));
            } else {
                $this->_shortaddress = $this->address;
            }
        }
        return $this->_shortaddress;
    }

    public int $selected = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => ''],
            [['updated_by'], 'default', 'value' => 1],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [['created_by', 'updated_by'], 'integer'],
            [['zip'], 'string', 'max' => 10],
            [['address1', 'address2', 'mail'], 'string', 'max' => 40],
            [['phone1', 'phone2'], 'string', 'max' => 20],
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
            'zip' => '郵便番号',
            'address' => '住所',
            'address1' => '住所',
            'address2' => '住所（続き）',
            'phone1' => '携帯電話',
            'phone2' => 'その他電話',
            'mail' => 'メール',
            'note' => 'メモ',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * Gets query for [[PersonContact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonContacts()
    {
        return $this->hasMany(PersonContact::class, ['contact_id' => 'id']);
    }

    /**
     * Gets query for [[Person]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersons()
    {
        return $this->hasMany(Person::class, ['id' => 'person_id'])
            ->viaTable('person_contact', ['contact_id' => 'id']);
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

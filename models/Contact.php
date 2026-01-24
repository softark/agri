<?php

namespace app\models;

use Yii;
use function PHPUnit\Framework\isEmpty;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property int $person_id
 * @property int $order
 * @property string $name1
 * @property string|null $name2
 * @property string $name
 * @property string|null $role
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
 * @property Person $person
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

    private $_disp_name = null;

    public function getDisp_name()
    {
        if ($this->_disp_name === null) {
            $this->_disp_name = $this->person->dispname;
            $fullname = $this->fullname;
            if ($fullname != '' && $fullname != $this->_disp_name) {
                $this->_disp_name = $this->_disp_name . ' / ' . $fullname;
            }
        }
        return $this->_disp_name;
    }

    private $_fullname = null;

    public function getFullName()
    {
        if ($this->_fullname === null) {
            $this->_fullname = trim($this->role . ' ' . $this->name1 . ' ' . $this->name2);
        }
        return $this->_fullname;
    }

    private $_contact_name = null;

    public function getContact_name()
    {
        if ($this->_contact_name === null) {
            $this->_contact_name = $this->getFullName();
            $person_name = $this->person->dispname;
            if ($this->_contact_name == $person_name) {
                $this->_contact_name = '';
            }
        }
        return $this->_contact_name;
    }

    private $_fulladdress = null;

    public function getFullAddress()
    {
        if ($this->_fulladdress === null) {
            $this->_fulladdress = trim($this->zip . ' ' . $this->address1 . $this->address2);
        }
        return $this->_fulladdress;
    }

    private $_phones = null;

    public function getPhones()
    {
        if ($this->_phones === null) {
            if ($this->phone1 != '') {
                if ($this->phone2 != '') {
                    $this->_phones = trim($this->phone1 . ' / ' . $this->phone2);
                } else {
                    $this->_phones = $this->phone1;
                }
            } else {
                if ($this->phone2 != '') {
                    $this->_phones = $this->phone2;
                } else {
                    $this->_phones = '';
                }
            }
        }
        return $this->_phones;
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
            [['person_id'], 'required'],
            [['person_id'], 'exist', 'targetClass' => Person::class, 'targetAttribute' => ['person_id' => 'id']],
            [['person_id', 'order', 'created_by', 'updated_by'], 'integer'],
            [['name1', 'name2'], 'string', 'max' => 30],
            [['order'], 'default', 'value' => 1],
            [['role'], 'string', 'max' => 30],
            [['zip'], 'string', 'max' => 10],
            [['address1', 'address2', 'mail'], 'string', 'max' => 40],
            [['mail'], 'email'],
            [['phone1', 'phone2'], 'string', 'max' => 20],
            [['note'], 'string', 'max' => 50],
            [['role', 'name1', 'name2', 'zip', 'address1', 'address2', 'phone1', 'phone2', 'mail', 'note'], 'default', 'value' => ''],
            ['note', 'required',
                'when' => function ($model) {
                    return ($model->role == '' && $model->name1 == '' && $model->name2 == ''
                        && $model->zip == '' && $model->address1 == '' && $model->address2 == ''
                        && $model->phone1 == '' && $model->phone2 == '' && $model->mail == '');
                },
                'whenClient' => "function (attribute, value) {
                    return (!$('#role').val().length && !$('#contact-name1').val().length && !$('#contact-name2').val().length
                    && !$('#zip').val().length && !$('#address1').val().length && !$('#address2').val().length
                    && !$('#phone1').val().length && !$('#phone2').val().length && !$('#mail').val().length);
                }",
                'message' => '全項目が空白です。どれも必須ではありませんが、一つは入力して下さい。'
            ],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],];
    }

    /**
     * {@inheritdoc}
     */
    public
    function attributeLabels()
    {
        return [
            'id' => 'ID',
            'person_id' => '関係者',
            'order' => '優先順位',
            'name1' => '連絡先名前半',
            'name2' => '連絡先名後半',
            'name' => '宛名',
            'disp_name' => '宛名',
            'fullname' => '宛名',
            'contact_name' => '連絡先',
            'role' => '役割／肩書',
            'zip' => '郵便番号',
            'address' => '住所',
            'address1' => '住所',
            'address2' => '住所（丁目・番地以降）',
            'fulladdress' => '住所',
            'phone1' => '電話（メイン）',
            'phone2' => '電話（その他）',
            'phones' => '電話',
            'mail' => 'メール',
            'note' => 'メモ',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * Gets query for [[Person]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'person_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
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

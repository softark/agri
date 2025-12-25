<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property int $id
 * @property string $name1
 * @property string|null $name2
 * @property string $name
 * @property string|null $yomi1
 * @property string|null $yomi2
 * @property string|null $yomi
 * @property string|null $org_name
 * @property string|null $zip
 * @property string|null $address
 * @property string|null $phone1
 * @property string|null $phone2
 * @property string|null $memo
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
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
            [['name2', 'yomi1', 'yomi2', 'org_name', 'zip', 'address', 'phone1', 'phone2', 'memo'], 'default', 'value' => null],
            [['updated_by'], 'default', 'value' => 1],
            [['name1'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [['created_by', 'updated_by'], 'integer'],
            [['name1', 'name2', 'yomi1', 'yomi2', 'org_name'], 'string', 'max' => 30],
            [['address', 'memo',], 'string', 'max' => 50],
            [['zip'], 'string', 'max' => 10],
            [['phone1', 'phone2'], 'string', 'max' => 20],
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
            'name1' => '姓',
            'name2' => '名',
            'name' => '名前',
            'dispname' => '名前',
            'yomi1' => 'よみがな（姓）',
            'yomi2' => 'よみがな（名）',
            'yomi' => 'よみがな',
            'yomigana' => 'よみがな',
            'org_name' => '団体名',
            'zip' => '郵便番号',
            'address' => '住所',
            'phone1' => '携帯電話',
            'phone2' => 'その他電話',
            'memo' => 'メモ',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
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

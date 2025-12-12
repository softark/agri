<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $dispname
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * property AuthAssignment[] $authAssignments
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @var string the user friendly name of the model used in user interface
     */
    const MODEL_NAME = 'ユーザ';

    /**
     * デフォルトのユーザ名形式
     */
    const DEFAULT_USERNAME_FORMAT = 'user-%d';
    const DEFAULT_USERNAME_PATTERN = '/^user\-\d+$/';
    /**
     * @var mixed|null
     */
    private  $_longName = '';

    /**
     * ユーザ種別
     */
    const USER_SYSTEM = 1;
    const USER_ADMIN = 2;
    const USER_NORMAL_START = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @var string パスワード
     */
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'message' => 'このユーザ名は既に登録されています。'],
            ['username', 'string', 'min' => 2, 'max' =>  32],
            ['username', 'match', 'pattern' => '/^[a-z][\w\-]*$/i',
                'message' => '{attribute} は、英字で始めて、英数字とハイフン、アンダーバーだけで構成して下さい。'],

            ['dispname', 'trim'],
            ['dispname', 'required'],
            ['dispname', 'unique', 'message' => 'この表示名は既に使用されています。'],
            ['dispname', 'string', 'min' => 2, 'max' => 32],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'on' => 'create', 'min' => 6],
            ['password', 'string', 'on' => 'update', 'skipOnEmpty' => true, 'min' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ユーザ名',
            'dispname' => '表示名',
            'longName' => '名前',
            'password' => 'パスワード',
            'password_hash' => 'パスワード・ハッシュ',
            'roleText' => '権限',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string 長い名前
     */
    public function getLongName()
    {
        if ($this->_longName == '') {
            $this->_longName = $this->username . ' [' . $this->dispname . ']';
        }
        return $this->_longName;
    }

    /**
     * @param int $length
     * @return string 短い名前
     */
    public function getShortName($length = 3)
    {
        return StringHelper::truncate($this->dispname, $length, '', 'UTF-8');
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getAuthAssignments()
//    {
//        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
//    }

    /**
     * @return \yii\rbac\Role[]
     */
    public function getRoles()
    {
        return Yii::$app->authManager->getRolesByUser($this->id);
    }

    /**
     * @param string $dispAttr
     * @param string $glue
     * @return string ロールを表す文字列
     */
    public function getRoleText($dispAttr = 'description', $glue = "\n")
    {
        $roles = $this->getRoles();
        $texts = [];
        foreach ($roles as $role) {
            if (!empty($role->$dispAttr)) {
                $texts[] = $role->$dispAttr;
            }
        }
        return implode($glue, $texts);
    }

    /**
     * @param $roleName
     * @return bool ロールを所有しているかどうか
     */
    public function hasRole($roleName)
    {
        /* @var $role \yii\rbac\Role */
        foreach ($this->roles as $role) {
            if ($role->name === $roleName) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool システムアカウントかどうか
     */
    public function getIsSystemAccount()
    {
        return $this->id < self::USER_NORMAL_START;
    }

    /**
     * @return bool 通常のアカウントかどうか
     */
    public function getIsNormalAccount()
    {
        return $this->id >= self::USER_NORMAL_START;
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
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
}

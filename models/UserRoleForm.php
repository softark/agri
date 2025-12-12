<?php


namespace app\models;

use Yii;
use yii\base\Model;

class UserRoleForm extends Model
{
    /**
     * @var integer ユーザID
     */
    public $user_id;

    /**
     * @var array ロール
     */
    public $roles;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['roles', 'each', 'rule' => ['validateRole']],
        ];
    }

    /**
     * 権限の検証
     * @param $attribute
     * @param $params
     * @param $validator
     * @return |null
     */
    public function validateRole($attribute, $params, $validator)
    {
        $role = Yii::$app->authManager->getRole($attribute);
        if ($role === false) {
            $this->addError($attribute, '{attribute} は、有効な権限ではありません。');
        }
        if (!Yii::$app->user->can('admin')) {
            if ($role->name == 'admin') {
                $this->addError($attribute, '\'admin\' の権限を与える権限はありません。');
            }
        }
        return null;
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ユーザ',
            'roles' => '権限',
        ];
    }

    /**
     * ユーザのロールを読み出す
     */
    public function loadUserRoles()
    {
        $this->roles = [];
        if ($this->user_id) {
            foreach(Yii::$app->authManager->getRolesByUser($this->user_id) as $roleObject) {
                $this->roles[] = $roleObject->name;
            }
        }
    }

    /**
     * @return array 割り当て可能なロールのリスト
     */
    public static function getAvailableRoleList()
    {
        $admin = Yii::$app->user->can('admin');
        $list = [];
        foreach (Yii::$app->authManager->getRoles() as $role) {
            if ($role->name == 'admin') {
                if ($admin) {
                    $list[$role->name] = $role->description;
                }
            } else if ($role->name != 'user'){
                $list[$role->name] = $role->description;
            }
        }
        return $list;
    }
}
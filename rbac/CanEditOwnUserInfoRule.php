<?php

namespace app\rbac;

use Yii;
use yii\rbac\Rule;

/**
 * Class CanManageOwnUserInfoRule
 * @package app\rbac
 *
 * 自分自身のユーザ情報を編集することが出来るかどうかをチェックする規則
 * 'user.editOwn' 許可に使用する
 */
class CanEditOwnUserInfoRule extends Rule
{
    public $name = 'canEditOwnUserInfo';

    /**
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) { // ログイン・ユーザ
            if (isset($params['id'])) {  // ユーザ情報の 'id'
                $id = intval($params['id']);
                return ($id == $user);   // 自分自身なら OK
            } else {
                return true;
            }
        }
        return false;  // 駄目
    }
}
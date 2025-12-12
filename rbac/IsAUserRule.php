<?php

namespace app\rbac;

use Yii;
use yii\rbac\Rule;

/**
 * Class IsAUserRule
 * @package app\rbac
 *
 * ログイン・ユーザであるかどうかを判断する規則。'user' ロールに使用する。
 * ログイン・ユーザであれば、'user' のロールを保持していると認める。
 */
class IsAUserRule extends Rule
{
    public $name = 'isAUser';

    /**
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        return !Yii::$app->user->isGuest;
    }
}
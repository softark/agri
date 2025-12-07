<?php

namespace app\rbac;

use yii\rbac\Rule;

/**
 * Class IsAGuestRule
 * @package app\rbac
 *
 * ユーザであるかどうかを判断する規則。'guest' ロールに使用する。
 * ユーザであれば、ログインしていてもしていなくても、'guest' のロールを保持していると認める。
 */
class IsAGuestRule extends Rule
{
    public $name = 'isAGuest';

    /**
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        return true;
    }
}
<?php

namespace app\rbac;

use Yii;
use yii\rbac\Rule;

/**
 * Class IsAMemberRule
 * @package app\rbac
 *
 * ログイン・ユーザであるかどうかを判断する規則。'member' ロールに使用する。
 * ログイン・ユーザであれば、'member' のロールを保持していると認める。
 */
class IsAMemberRule extends Rule
{
    public $name = 'isAMember';

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
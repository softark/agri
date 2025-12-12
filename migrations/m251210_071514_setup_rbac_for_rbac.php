<?php

use yii\db\Migration;

class m251210_071514_setup_rbac_for_rbac extends Migration
{
    /**
     * @return yii\rbac\DbManager
     * @throws yii\base\InvalidConfigException
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof yii\rbac\DbManager) {
            throw new yii\base\InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function up()
    {
        $auth = $this->getAuthManager();

        // "rbac" モジュールのルートを作成して追加
        $rbacRoute = $auth->createPermission('/rbac/*');
        $auth->add($rbacRoute);

        // 'admin' ロールに "/rbac/*" のルートを追加
        $adminRole = $auth->getRole('admin');
        $auth->addChild($adminRole, $rbacRoute);

        $auth->invalidateCache();

    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // "rbac" モジュールのルートを削除
        $rbacRoute = $auth->getPermission("/rbac/*");
        $auth->remove($rbacRoute);

        $auth->invalidateCache();
        // 親子関係とロール割当ては自動的に削除される
    }
}

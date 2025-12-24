<?php

use yii\db\Migration;

class m251224_043053_setup_rbac_for_forest extends Migration
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

        // 'forest' のルート
        $routeAll = $auth->createPermission('/forest/*');
        $auth->add($routeAll);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $routeAll);
        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'forest' のルートを削除
        $routeAll = $auth->getPermission('/forest/*');
        $auth->remove($routeAll);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}
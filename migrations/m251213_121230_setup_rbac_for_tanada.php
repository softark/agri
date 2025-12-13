<?php

use yii\db\Migration;

class m251213_121230_setup_rbac_for_tanada extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return yii\rbac\DbManager
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

        // 'tanada' のルート
        $routeAll = $auth->createPermission('/tanada/*');
        $auth->add($routeAll);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $routeAll);
        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'tanada' のルートを削除
        $routeAll = $auth->getPermission('/tanada/*');
        $auth->remove($routeAll);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

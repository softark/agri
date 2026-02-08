<?php

use yii\db\Migration;

class m260208_110905_modify_rbac_for_forest extends Migration
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

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = $this->getAuthManager();

        // 'forest' のルート ... 不要なものを削除
        $routeAddFp = $auth->getPermission('/forest/add-forest-person');
        $auth->remove($routeAddFp);
        $routeUpdateFp = $auth->getPermission('/forest/update-forest-person');
        $auth->remove($routeUpdateFp);

        // 'forest' のルート
        $routeDelFp = $auth->createPermission('/forest/delete-forest-person');
        $auth->add($routeDelFp);

        // 'forest.edit' 許可
        $forestEdit = $auth->getPermission('forest.edit');

        $auth->addChild($forestEdit, $routeDelFp);

        $auth->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = $this->getAuthManager();

        // 'forest' のルート
        $routeDelFp = $auth->getPermission('/forest/delete-forest-person');
        $auth->remove($routeDelFp);

        // 'forest' の旧いルート
        $routeAddFp = $auth->createPermission('/forest/add-forest-person');
        $auth->add($routeAddFp);
        $routeUpdateFp = $auth->createPermission('/forest/update-forest-person');
        $auth->add($routeUpdateFp);

        // 'forest.edit' 許可
        $forestEdit = $auth->getPermission('forest.edit');

        $auth->addChild($forestEdit, $routeAddFp);
        $auth->addChild($forestEdit, $routeUpdateFp);

        $auth->invalidateCache();
    }
}

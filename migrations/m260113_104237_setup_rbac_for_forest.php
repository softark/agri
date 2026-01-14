<?php

use yii\db\Migration;

class m260113_104237_setup_rbac_for_forest extends Migration
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

        // 'forest' のルート
        $routeIndex = $auth->createPermission('/forest/index');
        $auth->add($routeIndex);
        $routeView = $auth->createPermission('/forest/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/forest/update');
        $auth->add($routeUpdate);

        // 'forest.list' 許可
        $forestList = $auth->createPermission('forest.list');
        $forestList->description = '森林一覧';
        $auth->add($forestList);

        $auth->addChild($forestList, $routeIndex);

        // 'forest.view' 許可
        $forestView = $auth->createPermission('forest.view');
        $forestView->description = '森林閲覧';
        $auth->add($forestView);

        $auth->addChild($forestView, $routeView);

        // 'forest.edit' 許可
        $forestEdit = $auth->createPermission('forest.edit');
        $forestEdit->description = '森林編集';
        $auth->add($forestEdit);

        $auth->addChild($forestEdit, $routeUpdate);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $forestList);
        $auth->addChild($user, $forestView);

        // 'editor' ロール
        $admin = $auth->getRole("editor");

        $auth->addChild($admin, $forestEdit);

        // 'aza' のルート
        $routeAza = $auth->createPermission('/aza/*');
        $auth->add($routeAza);
        // 'frtype' のルート
        $routeFrtype = $auth->createPermission('/frtype/*');
        $auth->add($routeFrtype);

        // 'admin' ロール
        $admin = $auth->getRole("admin");
        $auth->addChild($admin, $routeAza);
        $auth->addChild($admin, $routeFrtype);

        $auth->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = $this->getAuthManager();

        // 'forest' のルートを削除
        $routeIndex = $auth->getPermission('/forest/index');
        $auth->remove($routeIndex);
        $routeView = $auth->getPermission('/forest/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/forest/update');
        $auth->remove($routeUpdate);

        // 'forest.list' 許可を削除
        $forestList = $auth->getPermission('forest.list');
        $auth->remove($forestList);

        // 'forest.view' 許可を削除
        $forestView = $auth->getPermission('forest.view');
        $auth->remove($forestView);

        // 'forest.edit' 許可を削除
        $forestEdit = $auth->getPermission('forest.edit');
        $auth->remove($forestEdit);

        // 'aza' のルート
        $routeAza = $auth->getPermission('/aza/*');
        $auth->remove($routeAza);
        // 'frtype' のルート
        $routeFrtype = $auth->getPermission('/frtype/*');
        $auth->remove($routeFrtype);
    }
}

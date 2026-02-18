<?php

use yii\db\Migration;

class m260217_055830_setup_rbac_of_usage extends Migration
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

        // 'usage' のルート
        $routeUsage = $auth->getPermission('/usage/*');
        $auth->remove($routeUsage);

        $routeIndex = $auth->createPermission('/usage/index');
        $auth->add($routeIndex);
        $routeView = $auth->createPermission('/usage/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/usage/update');
        $auth->add($routeUpdate);
        $routeCreate = $auth->createPermission('/usage/create');
        $auth->add($routeCreate);
        $routeDelete = $auth->createPermission('/usage/delete');
        $auth->add($routeDelete);

        // 'usage.list' 許可
        $usageList = $auth->createPermission('usage.list');
        $usageList->description = 'Usage 一覧';
        $auth->add($usageList);

        $auth->addChild($usageList, $routeIndex);

        // 'usage.view' 許可
        $usageView = $auth->createPermission('usage.view');
        $usageView->description = 'Usage 閲覧';
        $auth->add($usageView);

        $auth->addChild($usageView, $routeView);

        // 'usage.edit' 許可
        $usageEdit = $auth->createPermission('usage.edit');
        $usageEdit->description = 'Usage 編集';
        $auth->add($usageEdit);

        $auth->addChild($usageEdit, $routeUpdate);

        // 'usage.delete' 許可
        $usageDelete = $auth->createPermission('usage.delete');
        $usageDelete->description = 'Usage 削除';
        $auth->add($usageDelete);

        $auth->addChild($usageDelete, $routeDelete);

        // 'usage.create' 許可
        $usageCreate = $auth->createPermission('usage.create');
        $usageCreate->description = 'Usage 登録';
        $auth->add($usageCreate);

        $auth->addChild($usageCreate, $routeCreate);

        // 'editor' ロール
        $editor = $auth->getRole("editor");

        $auth->addChild($editor, $usageList);
        $auth->addChild($editor, $usageView);
        $auth->addChild($editor, $usageEdit);
        $auth->addChild($editor, $usageCreate);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $usageDelete);

        $auth->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = $this->getAuthManager();

        $routeIndex = $auth->getPermission('/usage/index');
        $auth->remove($routeIndex);
        $routeView = $auth->getPermission('/usage/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/usage/update');
        $auth->remove($routeUpdate);
        $routeCreate = $auth->getPermission('/usage/create');
        $auth->remove($routeCreate);
        $routeDelete = $auth->getPermission('/usage/delete');
        $auth->remove($routeDelete);

        // 'usage.list' 許可を削除
        $usageList = $auth->getPermission('usage.list');
        $auth->remove($usageList);

        // 'usage.view' 許可を削除
        $usageView = $auth->getPermission('usage.view');
        $auth->remove($usageView);

        // 'usage.edit' 許可を削除
        $usageEdit = $auth->getPermission('usage.edit');
        $auth->remove($usageEdit);

        // 'usage.create' 許可を削除
        $usageCreate = $auth->getPermission('usage.create');
        $auth->remove($usageCreate);

        // 'usage.delete' 許可を削除
        $usageDelete = $auth->getPermission('usage.delete');
        $auth->remove($usageDelete);

        // 'usage' のルート
        $routeUsage = $auth->createPermission('/usage/*');
        $auth->add($routeUsage);

        // 'admin' ロール
        $admin = $auth->getRole("admin");
        $auth->addChild($admin, $routeUsage);

        $auth->invalidateCache();
    }
}

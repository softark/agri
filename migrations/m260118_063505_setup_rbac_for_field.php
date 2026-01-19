<?php

use yii\db\Migration;

class m260118_063505_setup_rbac_for_field extends Migration
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

        // 'field' のルート
        $routeIndex = $auth->createPermission('/field/index');
        $auth->add($routeIndex);
        $routeView = $auth->createPermission('/field/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/field/update');
        $auth->add($routeUpdate);
        $routeAddFp = $auth->createPermission('/field/add-field-person');
        $auth->add($routeAddFp);
        $routeUpdateFp = $auth->createPermission('/field/update-field-person');
        $auth->add($routeUpdateFp);
        $routeAddFu = $auth->createPermission('/field/add-field-usage');
        $auth->add($routeAddFu);
        $routeUpdateFu = $auth->createPermission('/field/update-field-usage');
        $auth->add($routeUpdateFu);

        // 'field.list' 許可
        $fieldList = $auth->createPermission('field.list');
        $fieldList->description = '農地一覧';
        $auth->add($fieldList);

        $auth->addChild($fieldList, $routeIndex);

        // 'field.view' 許可
        $fieldView = $auth->createPermission('field.view');
        $fieldView->description = '農地閲覧';
        $auth->add($fieldView);

        $auth->addChild($fieldView, $routeView);

        // 'field.edit' 許可
        $fieldEdit = $auth->createPermission('field.edit');
        $fieldEdit->description = '農地編集';
        $auth->add($fieldEdit);

        $auth->addChild($fieldEdit, $routeUpdate);
        $auth->addChild($fieldEdit, $routeAddFp);
        $auth->addChild($fieldEdit, $routeUpdateFp);
        $auth->addChild($fieldEdit, $routeAddFu);
        $auth->addChild($fieldEdit, $routeUpdateFu);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $fieldList);
        $auth->addChild($user, $fieldView);

        // 'editor' ロール
        $admin = $auth->getRole("editor");

        $auth->addChild($admin, $fieldEdit);

        // 'usage' のルート
        $routeUsage = $auth->createPermission('/usage/*');
        $auth->add($routeUsage);

        // 'admin' ロール
        $admin = $auth->getRole("admin");
        $auth->addChild($admin, $routeUsage);

        $auth->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = $this->getAuthManager();

        // 'field' のルートを削除
        $routeIndex = $auth->getPermission('/field/index');
        $auth->remove($routeIndex);
        $routeView = $auth->getPermission('/field/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/field/update');
        $auth->remove($routeUpdate);
        $routeAddFp = $auth->getPermission('/field/add-field-person');
        $auth->remove($routeAddFp);
        $routeUpdateFp = $auth->getPermission('/field/update-field-person');
        $auth->remove($routeUpdateFp);
        $routeAddFu = $auth->getPermission('/field/add-field-usage');
        $auth->remove($routeAddFu);
        $routeUpdateFu = $auth->getPermission('/field/update-field-usage');
        $auth->remove($routeUpdateFu);

        // 'field.list' 許可を削除
        $fieldList = $auth->getPermission('field.list');
        $auth->remove($fieldList);

        // 'field.view' 許可を削除
        $fieldView = $auth->getPermission('field.view');
        $auth->remove($fieldView);

        // 'field.edit' 許可を削除
        $fieldEdit = $auth->getPermission('field.edit');
        $auth->remove($fieldEdit);

        // 'usage' のルート
        $routeUsage = $auth->getPermission('/usage/*');
        $auth->remove($routeUsage);
    }
}

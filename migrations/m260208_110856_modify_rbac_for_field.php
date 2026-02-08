<?php

use yii\db\Migration;

class m260208_110856_modify_rbac_for_field extends Migration
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

        // 'field' のルート ... 不要なものを削除
        $routeAddFp = $auth->getPermission('/field/add-field-person');
        $auth->remove($routeAddFp);
        $routeUpdateFp = $auth->getPermission('/field/update-field-person');
        $auth->remove($routeUpdateFp);
        $routeAddFu = $auth->getPermission('/field/add-field-usage');
        $auth->remove($routeAddFu);
        $routeUpdateFu = $auth->getPermission('/field/update-field-usage');
        $auth->remove($routeUpdateFu);

        // 'field' のルート
        $routeDelFp = $auth->createPermission('/field/delete-field-person');
        $auth->add($routeDelFp);
        $routeDelFu = $auth->createPermission('/field/delete-field-usage');
        $auth->add($routeDelFu);

        // 'field.edit' 許可
        $fieldEdit = $auth->getPermission('field.edit');

        $auth->addChild($fieldEdit, $routeDelFp);
        $auth->addChild($fieldEdit, $routeDelFu);

        $auth->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = $this->getAuthManager();

        // 'field' のルート
        $routeDelFp = $auth->getPermission('/field/delete-field-person');
        $auth->remove($routeDelFp);
        $routeDelFu = $auth->getPermission('/field/delete-field-usage');
        $auth->remove($routeDelFu);

        // 'field' の旧いルート
        $routeAddFp = $auth->createPermission('/field/add-field-person');
        $auth->add($routeAddFp);
        $routeUpdateFp = $auth->createPermission('/field/update-field-person');
        $auth->add($routeUpdateFp);
        $routeAddFu = $auth->createPermission('/field/add-field-usage');
        $auth->add($routeAddFu);
        $routeUpdateFu = $auth->createPermission('/field/update-field-usage');
        $auth->add($routeUpdateFu);

        // 'field.edit' 許可
        $fieldEdit = $auth->getPermission('field.edit');

        $auth->addChild($fieldEdit, $routeAddFp);
        $auth->addChild($fieldEdit, $routeUpdateFp);
        $auth->addChild($fieldEdit, $routeAddFu);
        $auth->addChild($fieldEdit, $routeUpdateFu);

        $auth->invalidateCache();
    }
}

<?php

use yii\db\Migration;

class m260123_021804_setup_rbac_for_excel extends Migration
{
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof yii\rbac\DbManager) {
            throw new yii\base\InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $auth = $this->getAuthManager();

        // ルート
        $routeField = $auth->createPermission('/field/export');
        $auth->add($routeField);
        $routeForest = $auth->createPermission('/forest/export');
        $auth->add($routeForest);
        $routePerson = $auth->createPermission('/person/export');
        $auth->add($routePerson);
        $routeContact = $auth->createPermission('/contact/export');
        $auth->add($routeContact);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $routeField);
        $auth->addChild($user, $routeForest);
        $auth->addChild($user, $routePerson);
        $auth->addChild($user, $routeContact);

        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        $routeField = $auth->getPermission('/field/export');
        $auth->remove($routeField);
        $routeForest = $auth->getPermission('/forest/export');
        $auth->remove($routeForest);
        $routePerson = $auth->getPermission('/person/export');
        $auth->remove($routePerson);
        $routeContact = $auth->getPermission('/contact/export');
        $auth->remove($routeContact);
    }
}

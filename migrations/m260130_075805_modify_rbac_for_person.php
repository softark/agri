<?php

use yii\db\Migration;

class m260130_075805_modify_rbac_for_person extends Migration
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

        // 'person' のルート
        $routeUpdateRelaton = $auth->createPermission('/person/update-relation');
        $auth->add($routeUpdateRelaton);
        $routeAddRelaton = $auth->createPermission('/person/add-relation');
        $auth->add($routeAddRelaton);
        $routeDeleteRelaton = $auth->createPermission('/person/delete-relation');
        $auth->add($routeDeleteRelaton);

        // 'person.edit' 許可
        $personEdit = $auth->getPermission('person.edit');
        $auth->addChild($personEdit, $routeUpdateRelaton);
        $auth->addChild($personEdit, $routeAddRelaton);
        $auth->addChild($personEdit, $routeDeleteRelaton);

        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'person' のルートを削除
        $routeUpdateRelation = $auth->getPermission('/person/update-relation');
        $auth->remove($routeUpdateRelation);
        $routeAddRelation = $auth->getPermission('/person/add-relation');
        $auth->remove($routeAddRelation);
        $routeDeleteRelation = $auth->getPermission('/person/delete-relation');
        $auth->remove($routeDeleteRelation);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

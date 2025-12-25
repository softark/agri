<?php

use yii\db\Migration;

class m251213_121248_setup_rbac_for_person extends Migration
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
        $routeIndex = $auth->createPermission('/person/index');
        $auth->add($routeIndex);
        $routeSelect = $auth->createPermission('/person/select');
        $auth->add($routeSelect);
        $routeView = $auth->createPermission('/person/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/person/update');
        $auth->add($routeUpdate);
        $routeCreate = $auth->createPermission('/person/create');
        $auth->add($routeCreate);
        $routeDelete = $auth->createPermission('/person/delete');
        $auth->add($routeDelete);
        $routeImport = $auth->createPermission('/person/import');
        $auth->add($routeImport);

        // 'person.list' 許可
        $personList = $auth->createPermission('person.list');
        $personList->description = '関係者一覧';
        $auth->add($personList);

        $auth->addChild($personList, $routeIndex);
        $auth->addChild($personList, $routeSelect);

        // 'person.view' 許可
        $personView = $auth->createPermission('person.view');
        $personView->description = '関係者閲覧';
        $auth->add($personView);

        $auth->addChild($personView, $routeView);

        // 'person.edit' 許可
        $personEdit = $auth->createPermission('person.edit');
        $personEdit->description = '関係者編集';
        $auth->add($personEdit);

        $auth->addChild($personEdit, $routeUpdate);

        // 'person.delete' 許可
        $personDelete = $auth->createPermission('person.delete');
        $personDelete->description = '関係者削除';
        $auth->add($personDelete);

        $auth->addChild($personDelete, $routeDelete);

        // 'person.create' 許可
        $personCreate = $auth->createPermission('person.create');
        $personCreate->description = '関係者登録';
        $auth->add($personCreate);

        $auth->addChild($personCreate, $routeCreate);

        // 'person.import' 許可
        $personImport = $auth->createPermission('person.import');
        $personImport->description = '関係者インポート';
        $auth->add($personImport);

        $auth->addChild($personImport, $routeImport);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $personList);
        $auth->addChild($user, $personView);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $personEdit);
        $auth->addChild($admin, $personCreate);
        $auth->addChild($admin, $personDelete);
        $auth->addChild($admin, $personImport);

        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'person' のルートを削除
        $routeIndex = $auth->getPermission('/person/index');
        $auth->remove($routeIndex);
        $routeSelect = $auth->getPermission('/person/select');
        $auth->remove($routeSelect);
        $routeView = $auth->getPermission('/person/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/person/update');
        $auth->remove($routeUpdate);
        $routeCreate = $auth->getPermission('/person/create');
        $auth->remove($routeCreate);
        $routeDelete = $auth->getPermission('/person/delete');
        $auth->remove($routeDelete);
        $routeImport = $auth->getPermission('/person/import');
        $auth->remove($routeImport);

        // 'person.list' 許可を削除
        $personList = $auth->getPermission('person.list');
        $auth->remove($personList);

        // 'person.view' 許可を削除
        $personView = $auth->getPermission('person.view');
        $auth->remove($personView);

        // 'person.edit' 許可を削除
        $personEdit = $auth->getPermission('person.edit');
        $auth->remove($personEdit);

        // 'person.create' 許可を削除
        $personCreate = $auth->getPermission('person.create');
        $auth->remove($personCreate);

        // 'person.delete' 許可を削除
        $personDelete = $auth->getPermission('person.delete');
        $auth->remove($personDelete);

        // 'person.import' 許可を削除
        $personImport = $auth->getPermission('person.import');
        $auth->remove($personImport);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

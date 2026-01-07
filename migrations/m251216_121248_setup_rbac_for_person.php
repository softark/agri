<?php

use yii\db\Migration;

class m251216_121248_setup_rbac_for_person extends Migration
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
        $routeUpdateContact = $auth->createPermission('/person/update-contact');
        $auth->add($routeUpdateContact);
        $routeCreateContact = $auth->createPermission('/person/create-contact');
        $auth->add($routeCreateContact);
        $routeDeleteContact = $auth->createPermission('/person/delete-contact');
        $auth->add($routeDeleteContact);
        $routeReorderContact = $auth->createPermission('/person/reorder-contact');
        $auth->add($routeReorderContact);

        // 'person.list' 許可
        $personList = $auth->createPermission('person.list');
        $personList->description = '名簿一覧';
        $auth->add($personList);

        $auth->addChild($personList, $routeIndex);
        $auth->addChild($personList, $routeSelect);

        // 'person.view' 許可
        $personView = $auth->createPermission('person.view');
        $personView->description = '名簿閲覧';
        $auth->add($personView);

        $auth->addChild($personView, $routeView);

        // 'person.edit' 許可
        $personEdit = $auth->createPermission('person.edit');
        $personEdit->description = '名簿編集';
        $auth->add($personEdit);

        $auth->addChild($personEdit, $routeUpdate);
        $auth->addChild($personEdit, $routeUpdateContact);
        $auth->addChild($personEdit, $routeReorderContact);

        // 'person.delete' 許可
        $personDelete = $auth->createPermission('person.delete');
        $personDelete->description = '名簿削除';
        $auth->add($personDelete);

        $auth->addChild($personDelete, $routeDelete);
        $auth->addChild($personDelete, $routeDeleteContact);

        // 'person.create' 許可
        $personCreate = $auth->createPermission('person.create');
        $personCreate->description = '名簿登録';
        $auth->add($personCreate);

        $auth->addChild($personCreate, $routeCreate);
        $auth->addChild($personCreate, $routeCreateContact);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $personList);
        $auth->addChild($user, $personView);

        // 'editor' ロール
        $admin = $auth->getRole("editor");

        $auth->addChild($admin, $personEdit);
        $auth->addChild($admin, $personCreate);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $personDelete);

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
        $routeUpdateContact = $auth->getPermission('/person/update-contact');
        $auth->remove($routeUpdateContact);
        $routeCreateContact = $auth->getPermission('/person/create-contact');
        $auth->remove($routeCreateContact);
        $routeDeleteContact = $auth->getPermission('/person/delete-contact');
        $auth->remove($routeDeleteContact);
        $routeReorderContact = $auth->getPermission('/person/reorder-contact');
        $auth->remove($routeReorderContact);

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

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

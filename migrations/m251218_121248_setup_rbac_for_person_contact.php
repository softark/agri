<?php

use yii\db\Migration;

class m251218_121248_setup_rbac_for_person_contact extends Migration
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

        // 'person-contact' のルート
        $routeIndex = $auth->createPermission('/person-contact/index');
        $auth->add($routeIndex);
        $routeSelect = $auth->createPermission('/person-contact/select');
        $auth->add($routeSelect);
        $routeView = $auth->createPermission('/person-contact/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/person-contact/update');
        $auth->add($routeUpdate);
        $routeCreate = $auth->createPermission('/person-contact/create');
        $auth->add($routeCreate);
        $routeDelete = $auth->createPermission('/person-contact/delete');
        $auth->add($routeDelete);

        // 'person_contact.list' 許可
        $personContactList = $auth->createPermission('person_contact.list');
        $personContactList->description = '名簿連絡先ブリッジ一覧';
        $auth->add($personContactList);

        $auth->addChild($personContactList, $routeIndex);
        $auth->addChild($personContactList, $routeSelect);

        // 'person_contact.view' 許可
        $personContactView = $auth->createPermission('person_contact.view');
        $personContactView->description = '名簿連絡先ブリッジ閲覧';
        $auth->add($personContactView);

        $auth->addChild($personContactView, $routeView);

        // 'person_contact.edit' 許可
        $personContactEdit = $auth->createPermission('person_contact.edit');
        $personContactEdit->description = '名簿連絡先ブリッジ編集';
        $auth->add($personContactEdit);

        $auth->addChild($personContactEdit, $routeUpdate);

        // 'person_contact.delete' 許可
        $personContactDelete = $auth->createPermission('person_contact.delete');
        $personContactDelete->description = '名簿連絡先ブリッジ削除';
        $auth->add($personContactDelete);

        $auth->addChild($personContactDelete, $routeDelete);

        // 'person_contact.create' 許可
        $personContactCreate = $auth->createPermission('person_contact.create');
        $personContactCreate->description = '名簿連絡先ブリッジ登録';
        $auth->add($personContactCreate);

        $auth->addChild($personContactCreate, $routeCreate);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $personContactList);
        $auth->addChild($user, $personContactView);

        // 'editor' ロール
        $admin = $auth->getRole("editor");

        $auth->addChild($admin, $personContactEdit);
        $auth->addChild($admin, $personContactCreate);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $personContactDelete);

        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'contact-contact' のルートを削除
        $routeIndex = $auth->getPermission('/person-contact/index');
        $auth->remove($routeIndex);
        $routeView = $auth->getPermission('/person-contact/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/person-contact/update');
        $auth->remove($routeUpdate);
        $routeCreate = $auth->getPermission('/person-contact/create');
        $auth->remove($routeCreate);
        $routeDelete = $auth->getPermission('/person-contact/delete');
        $auth->remove($routeDelete);

        // 'person_contact.list' 許可を削除
        $personContactList = $auth->getPermission('person_contact.list');
        $auth->remove($personContactList);

        // 'person_contact.view' 許可を削除
        $personContactView = $auth->getPermission('person_contact.view');
        $auth->remove($personContactView);

        // 'person_contact.edit' 許可を削除
        $personContactEdit = $auth->getPermission('person_contact.edit');
        $auth->remove($personContactEdit);

        // 'person_contact.create' 許可を削除
        $personContactCreate = $auth->getPermission('person_contact.create');
        $auth->remove($personContactCreate);

        // 'person_contact.delete' 許可を削除
        $personContactDelete = $auth->getPermission('person_contact.delete');
        $auth->remove($personContactDelete);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

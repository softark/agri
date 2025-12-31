<?php

use yii\db\Migration;

class m251217_121248_setup_rbac_for_contact extends Migration
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

        // 'contact' のルート
        $routeIndex = $auth->createPermission('/contact/index');
        $auth->add($routeIndex);
        $routeSelect = $auth->createPermission('/contact/select');
        $auth->add($routeSelect);
        $routeView = $auth->createPermission('/contact/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/contact/update');
        $auth->add($routeUpdate);
        $routeCreate = $auth->createPermission('/contact/create');
        $auth->add($routeCreate);
        $routeDelete = $auth->createPermission('/contact/delete');
        $auth->add($routeDelete);

        // 'contact.list' 許可
        $contactList = $auth->createPermission('contact.list');
        $contactList->description = '連絡先一覧';
        $auth->add($contactList);

        $auth->addChild($contactList, $routeIndex);
        $auth->addChild($contactList, $routeSelect);

        // 'contact.view' 許可
        $contactView = $auth->createPermission('contact.view');
        $contactView->description = '連絡先閲覧';
        $auth->add($contactView);

        $auth->addChild($contactView, $routeView);

        // 'contact.edit' 許可
        $contactEdit = $auth->createPermission('contact.edit');
        $contactEdit->description = '連絡先編集';
        $auth->add($contactEdit);

        $auth->addChild($contactEdit, $routeUpdate);

        // 'contact.delete' 許可
        $contactDelete = $auth->createPermission('contact.delete');
        $contactDelete->description = '連絡先削除';
        $auth->add($contactDelete);

        $auth->addChild($contactDelete, $routeDelete);

        // 'contact.create' 許可
        $contactCreate = $auth->createPermission('contact.create');
        $contactCreate->description = '連絡先登録';
        $auth->add($contactCreate);

        $auth->addChild($contactCreate, $routeCreate);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $contactList);
        $auth->addChild($user, $contactView);

        // 'editor' ロール
        $admin = $auth->getRole("editor");

        $auth->addChild($admin, $contactEdit);
        $auth->addChild($admin, $contactCreate);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $contactDelete);

        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'contact' のルートを削除
        $routeIndex = $auth->getPermission('/contact/index');
        $auth->remove($routeIndex);
        $routeSelect = $auth->getPermission('/contact/select');
        $auth->remove($routeSelect);
        $routeView = $auth->getPermission('/contact/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/contact/update');
        $auth->remove($routeUpdate);
        $routeCreate = $auth->getPermission('/contact/create');
        $auth->remove($routeCreate);
        $routeDelete = $auth->getPermission('/contact/delete');
        $auth->remove($routeDelete);

        // 'contact.list' 許可を削除
        $contactList = $auth->getPermission('contact.list');
        $auth->remove($contactList);

        // 'contact.view' 許可を削除
        $contactView = $auth->getPermission('contact.view');
        $auth->remove($contactView);

        // 'contact.edit' 許可を削除
        $contactEdit = $auth->getPermission('contact.edit');
        $auth->remove($contactEdit);

        // 'contact.create' 許可を削除
        $contactCreate = $auth->getPermission('contact.create');
        $auth->remove($contactCreate);

        // 'contact.delete' 許可を削除
        $contactDelete = $auth->getPermission('contact.delete');
        $auth->remove($contactDelete);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

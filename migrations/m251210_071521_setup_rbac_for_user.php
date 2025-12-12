<?php

use yii\db\Migration;

class m251210_071521_setup_rbac_for_user extends Migration
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

        // 'user' のルート
        $routeIndex = $auth->createPermission('/user/index');
        $auth->add($routeIndex);
        $routeView = $auth->createPermission('/user/view');
        $auth->add($routeView);
        $routeUpdate = $auth->createPermission('/user/update');
        $auth->add($routeUpdate);
        $routeCreate = $auth->createPermission('/user/create');
        $auth->add($routeCreate);
        $routeDelete = $auth->createPermission('/user/delete');
        $auth->add($routeDelete);
        $routeRole = $auth->createPermission('/user/role');
        $auth->add($routeRole);

        // 'user.list' 許可
        $userList = $auth->createPermission('user.list');
        $userList->description = 'ユーザ一覧';
        $auth->add($userList);

        $auth->addChild($userList, $routeIndex);

        // 'user.view' 許可
        $userView = $auth->createPermission('user.view');
        $userView->description = 'ユーザ閲覧';
        $auth->add($userView);

        $auth->addChild($userView, $routeView);

        // 'user.edit' 許可
        $userEdit = $auth->createPermission('user.edit');
        $userEdit->description = 'ユーザ編集';
        $auth->add($userEdit);

        $auth->addChild($userEdit, $routeUpdate);

        // 'user.delete' 許可
        $userDelete = $auth->createPermission('user.delete');
        $userDelete->description = 'ユーザ削除';
        $auth->add($userDelete);

        $auth->addChild($userDelete, $routeDelete);

        // 'user.create' 許可
        $userCreate = $auth->createPermission('user.create');
        $userCreate->description = 'ユーザ登録';
        $auth->add($userCreate);

        $auth->addChild($userCreate, $routeCreate);

        // 'canEditOwnUserInfo' 規則を追加
        // 自分自身のユーザ情報を編集する権限の有無をチェックする規則
        $canEditOwnUserInfoRule = new \app\rbac\CanEditOwnUserInfoRule();
        $auth->add($canEditOwnUserInfoRule);

        // 'user.edit_own' 許可
        $userEditOwn = $auth->createPermission('user.edit_own');
        $userEditOwn->description = 'ユーザ自己編集';
        $userEditOwn->ruleName = $canEditOwnUserInfoRule->name;
        $auth->add($userEditOwn);

        $auth->addChild($userEditOwn, $userView);
        $auth->addChild($userEditOwn, $userEdit);

        // 'user.edit_role' 許可
        $userEditRole = $auth->createPermission('user.edit_role');
        $userEditRole->description = 'ユーザ・ロール編集';
        $auth->add($userEditRole);

        $auth->addChild($userEditRole, $routeRole);

        // 'user' ロール
        $user = $auth->getRole("user");

        $auth->addChild($user, $userEditOwn);
        $auth->addChild($user, $userList);

        // 'admin' ロール
        $admin = $auth->getRole("admin");

        $auth->addChild($admin, $userView);
        $auth->addChild($admin, $userEdit);
        $auth->addChild($admin, $userCreate);
        $auth->addChild($admin, $userDelete);
        $auth->addChild($admin, $userEditRole);

        $auth->invalidateCache();
    }

    public function down()
    {
        $auth = $this->getAuthManager();

        // 'user' のルートを削除
        $routeIndex = $auth->getPermission('/user/index');
        $auth->remove($routeIndex);
        $routeView = $auth->getPermission('/user/view');
        $auth->remove($routeView);
        $routeUpdate = $auth->getPermission('/user/update');
        $auth->remove($routeUpdate);
        $routeCreate = $auth->getPermission('/user/create');
        $auth->remove($routeCreate);
        $routeDelete = $auth->getPermission('/user/delete');
        $auth->remove($routeDelete);
        $routeRole = $auth->getPermission('/user/role');
        $auth->remove($routeRole);

        // 'user.list' 許可を削除
        $userList = $auth->getPermission('user.list');
        $auth->remove($userList);

        // 'user.view' 許可を削除
        $userView = $auth->getPermission('user.view');
        $auth->remove($userView);

        // 'user.edit' 許可を削除
        $userEdit = $auth->getPermission('user.edit');
        $auth->remove($userEdit);

        // 'user.create' 許可を削除
        $userCreate = $auth->getPermission('user.create');
        $auth->remove($userCreate);

        // 'user.delete' 許可を削除
        $userDelete = $auth->getPermission('user.delete');
        $auth->remove($userDelete);

        // 'user.edit_own' 許可を削除
        $userEditOwn = $auth->getPermission('user.edit_own');
        $auth->remove($userEditOwn);

        // 'canEditOwnUserInfo' 規則を削除
        $canEditOwnUserInfoRule = new \app\rbac\CanEditOwnUserInfoRule();
        $auth->remove($canEditOwnUserInfoRule);

        // 'user.edit_role' 許可を削除
        $userEditRole = $auth->getPermission('user.edit_role');
        $auth->remove($userEditRole);

        // 親子関係とロール割当ては自動的に削除される
        $auth->invalidateCache();
    }
}

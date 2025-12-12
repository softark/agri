<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

require_once ('builtInUsers.php');

class m251125_094632_setup_rbac_basics extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = $this->getAuthManager();

        // 'isAUser' 規則を追加
        $isAUserRule = new \app\rbac\IsAUserRule();
        $auth->add($isAUserRule);

        // 'user' ロールを追加
        $userRole = $auth->createRole('user');
        $userRole->description = 'ログイン･ユーザ';
        $userRole->ruleName = $isAUserRule->name;
        $auth->add($userRole);

        // 'editor' 「編集者」ロールを追加
        $editorRole = $auth->createRole('editor');
        $editorRole->description = '編集者';
        $auth->add($editorRole);

        // 'editor' は子として 'user' を持つ
        $auth->addChild($editorRole, $userRole);

        // 'admin' ロールを追加
        $adminRole = $auth->createRole('admin');
        $adminRole->description = '管理者';
        $auth->add($adminRole);

        // 'admin' は子として 'editor' を持つ
        $auth->addChild($adminRole, $editorRole);

        // ユーザ 'system' に 'admin' ロールを割当て
        $auth->assign($adminRole, SYSTEM_USER_ID);

        // ユーザ  'admin' に 'admin' ロールを割当て
        $auth->assign($adminRole, ADMIN_USER_ID);

        $auth->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = $this->getAuthManager();

        // 'admin' ロールを削除
        $adminRole = $auth->getRole('admin');
        $auth->remove($adminRole);

        // 'editor' ロールを削除
        $editorRole = $auth->getRole('editor');
        $auth->remove($editorRole);

        // 'user' ロールを削除
        $userRole = $auth->getRole('user');
        $auth->remove($userRole);

        // 'IsAUser' ルールを削除
        $isAUserRule = new \app\rbac\IsAUserRule();
        $auth->remove($isAUserRule);

        // 親子関係とロール割当ては自動的に削除される

        $auth->invalidateCache();
    }
}

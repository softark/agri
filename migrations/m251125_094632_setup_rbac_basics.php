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

        // 'isAMember' 規則を追加
        $isAMemberRule = new \app\rbac\IsAMemberRule();
        $auth->add($isAMemberRule);

        // 'member' ロールを追加
        $memberRole = $auth->createRole('member');
        $memberRole->description = 'ログイン･ユーザ';
        $memberRole->ruleName = $isAMemberRule->name;
        $auth->add($memberRole);

        // 'board' 「役員」ロールを追加
        $boardRole = $auth->createRole('board');
        $boardRole->description = '役員';
        $auth->add($boardRole);

        // 'board' は子として 'member' を持つ
        $auth->addChild($boardRole, $memberRole);

        // 'admin' ロールを追加
        $adminRole = $auth->createRole('admin');
        $adminRole->description = '管理者';
        $auth->add($adminRole);

        // 'admin' は子として 'board' を持つ
        $auth->addChild($adminRole, $boardRole);

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

        // 'board' ロールを削除
        $boardRole = $auth->getRole('board');
        $auth->remove($boardRole);

        // 'member' ロールを削除
        $memberRole = $auth->getRole('member');
        $auth->remove($memberRole);

        // 'IsAMember' ルールを削除
        $isAMemberRule = new \app\rbac\IsAMemberRule();
        $auth->remove($isAMemberRule);

        // 親子関係とロール割当ては自動的に削除される

        $auth->invalidateCache();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251125_094632_setup_rbac_basics cannot be reverted.\n";

        return false;
    }
    */
}

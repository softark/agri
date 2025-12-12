<?php

use yii\db\Migration;
use yii\db\Schema;

require_once ('builtInUsers.php');

/**
 * Handles the creation of table `{{%user}}`.
 */
class m251125_094525_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(32)->notNull()->unique(),
            'dispname' => $this->string(32)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);

        // システム・ユーザの作成
        $this->createSystemUsers();
    }

    /**
     * システムユーザの作成
     */
    public function createSystemUsers()
    {
        // create 'system' account
        $this->insert('{{%user}}', [
            'id' => SYSTEM_USER_ID,
            'username' => SYSTEM_USER_NAME,
            'dispname' => 'システム',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('kj083#1kid'),
        ]);

        // create 'admin' account
        $this->insert('{{%user}}', [
            'id' => ADMIN_USER_ID,
            'username' => ADMIN_USER_NAME,
            'dispname' => 'サイト管理者',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('isa563rigami'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}

<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%session}}`.
 */
class m251125_035125_create_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%session}}', [
            'id' => Schema::TYPE_CHAR . '(40) NOT NULL PRIMARY KEY',
            'expire' => $this->integer()->notNull(),
            'data' => 'bytea',
        ]);
        // インデックス
        $this->createIndex('ix_expire', 'session', 'expire');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%session}}');
    }
}

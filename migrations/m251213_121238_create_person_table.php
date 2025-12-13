<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%person}}`.
 */
class m251213_121238_create_person_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%person}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'yomigana' => $this->string(50)->null(),
            'address' => $this->string(50)->null(),
            'phone' => $this->string(16)->null(),
            'memo' => $this->string(50)->null(),
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);

        // インデックス
        $this->createIndex('ix_name', '{{%person}}', 'name');
        $this->createIndex('ix_yomigana', '{{%person}}', 'yomigana');
        $this->createIndex('ix_address', '{{%person}}', 'address');
        $this->createIndex('ix_phone', '{{%person}}', 'phone');
        $this->createIndex('ix_created_by', '{{%person}}', 'created_by');
        $this->createIndex('ix_created_at', '{{%person}}', 'created_at');
        $this->createIndex('ix_updated_by', '{{%person}}', 'updated_by');
        $this->createIndex('ix_updated_at', '{{%person}}', 'updated_at');
        // 外部キー
        $this->addForeignKey('fk_person_created_by_user_id', '{{%person}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_person_updated_by_user_id', '{{%person}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%person}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%person_work}}`.
 */
class m251220_024626_create_person_work_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%person_work}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60)->notNull(),
            'address' => $this->string(100)->null(),
            'src' => $this->integer()->notNull()->defaultValue(0),
            'person_id' => $this->integer()->null(),
            'contact_id' => $this->integer()->null(),
        ]);
        // インデックス
        $this->createIndex('ix_person_work_name', '{{%person_work}}', 'name');
        $this->createIndex('ix_person_work_address', '{{%person_work}}', 'address');
        $this->createIndex('ix_person_work_person_id', '{{%person_work}}', 'person_id');
        // 外部キー
        $this->addForeignKey('fk_person_work_person_id_person_id', '{{%person_work}}', 'person_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_person_work_contact_id_contact_id', '{{%person_work}}', 'contact_id', '{{%contact}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%person_work}}');
    }
}

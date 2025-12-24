<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

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
            'name1' => $this->string(30)->notNull(),
            'name2' => $this->string(30)->null(),
            'name' => Schema::TYPE_STRING . ' GENERATED ALWAYS as (name1 || name2) STORED',
            'yomi1' => $this->string(30)->null(),
            'yomi2' => $this->string(30)->null(),
            'yomi' => Schema::TYPE_STRING . ' GENERATED ALWAYS as (yomi1 || yomi2) STORED',
            'org_name' => $this->string(30)->null(),
            'zip' => $this->string(10)->null(),
            'address' => $this->string(50)->null(),
            'phone1' => $this->string(20)->null(),
            'phone2' => $this->string(20)->null(),
            'memo' => $this->string(50)->null(),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);

        // インデックス
        $this->createIndex('ix_person_name1', '{{%person}}', 'name1');
        $this->createIndex('ix_person_name2', '{{%person}}', 'name2');
        $this->createIndex('ix_person_name', '{{%person}}', 'name');
        $this->createIndex('ix_person_yomi1', '{{%person}}', 'yomi1');
        $this->createIndex('ix_person_yomi2', '{{%person}}', 'yomi2');
        $this->createIndex('ix_person_yomi', '{{%person}}', 'yomi');
        $this->createIndex('ix_person_org_name', '{{%person}}', 'org_name');
        $this->createIndex('ix_person_zip', '{{%person}}', 'zip');
        $this->createIndex('ix_person_address', '{{%person}}', 'address');
        $this->createIndex('ix_person_phone1', '{{%person}}', 'phone1');
        $this->createIndex('ix_person_phone2', '{{%person}}', 'phone2');
        $this->createIndex('ix_person_created_by', '{{%person}}', 'created_by');
        $this->createIndex('ix_person_created_at', '{{%person}}', 'created_at');
        $this->createIndex('ix_person_updated_by', '{{%person}}', 'updated_by');
        $this->createIndex('ix_person_updated_at', '{{%person}}', 'updated_at');
        // 外部キー
        $this->addForeignKey('fk_person_created_by_user_id', '{{%person}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_person_updated_by_user_id', '{{%person}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->seed();
    }

    public function seed()
    {
        $path = Yii::getAlias('@app/migrations/data/person-seed.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $used_cols = ['name1', 'name2', 'yomi1', 'yomi2', 'org_name', 'zip', 'address', 'phone1', 'phone2', 'memo'];
        $keys = array_flip($used_cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%person}}', $assoc);
        }
        fclose($fp);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%person}}');
    }
}

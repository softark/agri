<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Handles the creation of table `{{%person}}`.
 */
class m251215_121238_create_person_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%person}}', [
            'id' => $this->primaryKey(),
            'name1' => $this->string(30)->notNull(),
            'name2' => $this->string(30)->null()->defaultValue(''),
            'name' => $this->string(60) . ' GENERATED ALWAYS as (name1 || name2) STORED',
            'yomi1' => $this->string(30)->null()->defaultValue(''),
            'yomi2' => $this->string(30)->null()->defaultValue(''),
            'yomi' => $this->string(60) . ' GENERATED ALWAYS as (yomi1 || yomi2) STORED',
            'type' => $this->integer()->notNull()->defaultValue(1),
            'note' => $this->string()->null()->defaultValue(''),
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
        $this->createIndex('ix_person_type', '{{%person}}', 'type');
        // 外部キー
        $this->addForeignKey('fk_person_created_by_user_id', '{{%person}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_person_updated_by_user_id', '{{%person}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%contact}}', [
            'id' => $this->primaryKey(),
            'zip' => $this->string(10)->null()->defaultValue(''),
            'address1' => $this->string(40)->null()->defaultValue(''),
            'address2' => $this->string(40)->null()->defaultValue(''),
            'phone1' => $this->string(20)->null()->defaultValue(''),
            'phone2' => $this->string(20)->null()->defaultValue(''),
            'mail' => $this->string(40)->null()->defaultValue(''),
            'note' => $this->string(50)->null()->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);

        // インデックス
        $this->createIndex('ix_contact_zip', '{{%contact}}', 'zip');
        $this->createIndex('ix_contact_address1', '{{%contact}}', 'address1');
        $this->createIndex('ix_contact_phone1', '{{%contact}}', 'phone1');
        $this->createIndex('ix_contact_phone2', '{{%contact}}', 'phone2');
        $this->createIndex('ix_contact_mail', '{{%contact}}', 'mail');
        $this->createIndex('ix_contact_created_by', '{{%contact}}', 'created_by');
        $this->createIndex('ix_contact_created_at', '{{%contact}}', 'created_at');
        $this->createIndex('ix_contact_updated_by', '{{%contact}}', 'updated_by');
        $this->createIndex('ix_contact_updated_at', '{{%contact}}', 'updated_at');
        // 外部キー
        $this->addForeignKey('fk_contact_created_by_user_id', '{{%contact}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_contact_updated_by_user_id', '{{%contact}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%person_contact}}', [
            'person_id' => $this->integer()->notNull(),
            'contact_id' => $this->integer()->notNull(),
            'contact_name' => $this->string(60)->null()->defaultValue(''),
            'role' => $this->string(30)->null()->defaultValue(''),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'note' => $this->string(50)->null()->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);

        // インデックス
        $this->addPrimaryKey('ix_primary_person_contact', '{{%person_contact}}', ['person_id', 'contact_id']);
        $this->createIndex('ix_person_contact_order', '{{%person_contact}}', 'order');
        $this->createIndex('ix_person_contact_created_by', '{{%person_contact}}', 'created_by');
        $this->createIndex('ix_person_contact_created_at', '{{%person_contact}}', 'created_at');
        $this->createIndex('ix_person_contact_updated_by', '{{%person_contact}}', 'updated_by');
        $this->createIndex('ix_person_contact_updated_at', '{{%person_contact}}', 'updated_at');
        // 外部キー
        $this->addForeignKey('fk_person_contact_person_id_person_id', '{{%person_contact}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_person_contact_person_id_contact_id', '{{%person_contact}}', 'contact_id', '{{%contact}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_person_contact_created_by_user_id', '{{%person_contact}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_person_contact_updated_by_user_id', '{{%person_contact}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        // $this->seed();
    }

    public function seed()
    {
        $path = Yii::getAlias('@app/migrations/data/person-seed.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $p_cols = ['name1', 'name2', 'yomi1', 'yomi2', 'note'];
        $p_keys = array_flip($p_cols);
        $c_cols = ['zip', 'address1', 'phone1', 'phone2', 'note'];
        $c_keys = array_flip($c_cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $p_assoc = array_intersect_key($assoc, $p_keys);
            $this->insert('{{%person}}', $p_assoc);
            $c_assoc = array_intersect_key($assoc, $c_keys);
            if ($c_assoc['zip'] != '' || $c_assoc['address1'] != '' || $c_assoc['phone1'] != '' || $c_assoc['phone2'] != '') {
                $this->insert('{{%contact}}', $c_assoc);
            }
        }
        fclose($fp);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%person_contact}}');
        $this->dropTable('{{%contact}}');
        $this->dropTable('{{%person}}');
    }
}

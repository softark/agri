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
            'status' => $this->integer()->notNull()->defaultValue(1),
            'type' => $this->integer()->notNull()->defaultValue(1),
            'name1' => $this->string(30)->notNull(),
            'name2' => $this->string(30)->null()->defaultValue(''),
            'name' => $this->string(60) . ' GENERATED ALWAYS as (name1 || name2) STORED',
            'yomi1' => $this->string(30)->null()->defaultValue(''),
            'yomi2' => $this->string(30)->null()->defaultValue(''),
            'yomi' => $this->string(60) . ' GENERATED ALWAYS as (yomi1 || yomi2) STORED',
            'note' => $this->string(50)->null()->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);

        // インデックス
        $this->createIndex('ix_person_names', '{{%person}}', ['name1', 'name2'], true);
        $this->createIndex('ix_person_name', '{{%person}}', 'name', true);
        $this->createIndex('ix_person_yomi', '{{%person}}', 'yomi', false);
        $this->createIndex('ix_person_type', '{{%person}}', 'type');
        $this->createIndex('ix_person_status', '{{%person}}', 'status');
        // 外部キー
        $this->addForeignKey('fk_person_created_by_user_id', '{{%person}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_person_updated_by_user_id', '{{%person}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%person_relation}}', [
            'id' => $this->primaryKey(),
            'from_person_id' => $this->integer()->notNull(),
            'to_person_id' => $this->integer()->notNull(),
            'note' => $this->string(50)->null()->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->createIndex('ix_person_relation_pkey', '{{%person_relation}}', ['from_person_id', 'to_person_id'], true);
        // 外部キー
        $this->addForeignKey('fk_person_relation_from_person_id', '{{%person_relation}}', 'from_person_id', '{{%person}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_person_relation_to_person_id', '{{%person_relation}}', 'to_person_id', '{{%person}}', 'id', 'CASCADE', 'RESTRICT');

        $this->createTable('{{%contact}}', [
            'id' => $this->primaryKey(),
            'person_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull()->defaultValue(1),
            'role' => $this->string(30)->null()->defaultValue(''),
            'name1' => $this->string(30)->null()->defaultValue(''),
            'name2' => $this->string(30)->null()->defaultValue(''),
            'name' => $this->string(60) . ' GENERATED ALWAYS as (name1 || name2) STORED',
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
        $this->createIndex('ix_contact_person_id_order', '{{%contact}}', ['person_id', 'order'], true);
        $this->createIndex('ix_contact_name', '{{%contact}}', 'name');
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
        $this->addForeignKey('fk_contact_person_id_person_id', '{{%contact}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_contact_created_by_user_id', '{{%contact}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_contact_updated_by_user_id', '{{%contact}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->seedPersons();
        $this->seedContacts();
        $this->seedPersonRelations();
    }

    public function seedPersons()
    {
        $path = Yii::getAlias('@app/migrations/data/person.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $cols = ['id', 'name1', 'name2', 'yomi1', 'yomi2', 'type', 'status', 'note'];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%person}}', $assoc);
        }
        fclose($fp);

        $this->execute('alter sequence person_id_seq restart with 91');
    }

    public function seedContacts()
    {
        $path = Yii::getAlias('@app/migrations/data/contact.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $cols = ['id', 'person_id', 'order', 'role', 'name1', 'name2', 'zip', 'address1', 'address2', 'phone1', 'phone2', 'mail', 'note'];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%contact}}', $assoc);
        }
        fclose($fp);
        $this->execute('alter sequence contact_id_seq restart with 108');
    }

    public function seedPersonRelations()
    {
        $path = Yii::getAlias('@app/migrations/data/person_relation.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $cols = ['id', 'from_person_id', 'to_person_id', 'note'];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%person_relation}}', $assoc);
        }
        fclose($fp);
        $this->execute('alter sequence contact_id_seq restart with 19');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contact}}');
        $this->dropTable('{{%person_relation}}');
        $this->dropTable('{{%person}}');
    }
}

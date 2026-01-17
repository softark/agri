<?php

use yii\db\Migration;
use yii\db\Schema;

class m260113_002552_create_master_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%aza}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(10)->notNull(),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->createIndex('ix_aza_name', '{{%aza}}', 'name');
        // 外部キー
        $this->addForeignKey('fk_aza_created_by_user_id', '{{%aza}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_aza_updated_by_user_id', '{{%aza}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%frtype}}', [
            'id' => $this->primaryKey(),
            'order' => $this->integer()->notNull()->defaultValue(100),
            'name' => $this->string(10)->notNull(),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->createIndex('ix_frtype_name', '{{%frtype}}', 'name');
        $this->createIndex('ix_frtype_order', '{{%frtype}}', 'order');
        // 外部キー
        $this->addForeignKey('fk_frtype_created_by_user_id', '{{%frtype}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_frtype_updated_by_user_id', '{{%frtype}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->seedAza();
        $this->seedFrType();
    }

    public function seedAza()
    {
        /*
        $rows = (new \yii\db\Query())
            ->select(['ko_aza'])
            ->from('isg.forest')
            ->where(['not', ['ko_aza' => null]])
            ->distinct()
            ->orderBy('ko_aza')
            ->all();
        foreach ($rows as $row) {
            $this->insert('aza', ['name' => $row['ko_aza']]);
        }
        */
        $path = Yii::getAlias('@app/migrations/data/aza.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $cols = ['name'];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%aza}}', $assoc);
        }
        fclose($fp);
    }

    public function seedFrType()
    {
        /*
        $rows = (new \yii\db\Query())
            ->select(['type'])
            ->from('isg.forest')
            ->where(['not', ['type' => null]])
            ->distinct()
            ->orderBy('type')
            ->all();
        foreach ($rows as $row) {
            $this->insert('frtype', ['name' => $row['type']]);
        }
        */
        $path = Yii::getAlias('@app/migrations/data/frtype.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $cols = ['order', 'name'];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%frtype}}', $assoc);
        }
        fclose($fp);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%aza}}');
        $this->dropTable('{{%frtype}}');
    }
}

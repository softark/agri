<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%usage}}`.
 */
class m260116_135137_create_usage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%usage}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull()->defaultValue(0),
            'order' => $this->integer()->notNull()->defaultValue(100),
            'name' => $this->string(30)->notNull(),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->createIndex('ix_usage_type', '{{%usage}}', 'type');
        $this->createIndex('ix_usage_order', '{{%usage}}', 'order');
        $this->createIndex('ix_usage_name', '{{%usage}}', 'name');
        // 外部キー
        $this->addForeignKey('fk_usage_created_by_user_id', '{{%usage}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_usage_updated_by_user_id', '{{%usage}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->seedUsage();
    }

    public function seedUsage()
    {
        /*
        $rows = (new \yii\db\Query())
            ->select(['usage'])
            ->from('isg.tanada')
            ->where(['not', ['usage' => null]])
            ->distinct()
            ->orderBy('usage')
            ->all();
        foreach ($rows as $row) {
            $this->insert('usage', ['name' => $row['usage']]);
        }
        */
        $path = Yii::getAlias('@app/migrations/data/usage.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException("Cannot open: $path");

        $cols = ["id","type","order","name"];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%usage}}', $assoc);
        }
        fclose($fp);
        $this->execute('alter sequence usage_id_seq restart with 12');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%usage}}');
    }
}

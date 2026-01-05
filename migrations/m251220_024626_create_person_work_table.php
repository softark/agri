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
            'address' => $this->string(100)->null()->defaultValue(''),
            'src' => $this->integer()->notNull()->defaultValue(0),
            'person_id' => $this->integer()->null(),
        ]);
        // インデックス
        $this->createIndex('ix_person_work_name', '{{%person_work}}', 'name');
        $this->createIndex('ix_person_work_address', '{{%person_work}}', 'address');
        $this->createIndex('ix_person_work_person_id', '{{%person_work}}', 'person_id');
        // 外部キー
        $this->addForeignKey('fk_person_work_person_id_person_id', '{{%person_work}}', 'person_id', '{{%person}}', 'id', 'SET NULL', 'RESTRICT');

        $this->seed();
    }

    public function seed()
    {
        $path = Yii::getAlias('@app/migrations/data/person_work.csv');
        $fp = fopen($path, 'r');
        if (!$fp) throw new \RuntimeException('Cannot open: $path');

        $cols = ['name', 'address', 'src', 'person_id'];
        $keys = array_flip($cols);

        $header = fgetcsv($fp);               // 1行目を列名にする想定
        while (($row = fgetcsv($fp)) !== false) {
            $assoc = array_combine($header, $row);
            $assoc = array_intersect_key($assoc, $keys);
            $this->insert('{{%person_work}}', $assoc);
        }
        fclose($fp);

        $this->execute('alter sequence person_work_id_seq restart with 148');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%person_work}}');
    }
}

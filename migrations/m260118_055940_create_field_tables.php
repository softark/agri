<?php

use yii\db\Migration;
use yii\db\Schema;

class m260118_055940_create_field_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS postgis');

        $this->createTable('{{%field}}', [
            'id' => $this->primaryKey(),
            'geom' => 'public.geometry(MultiPolygon,6673) NOT NULL',
            'aza_id' => $this->integer()->defaultValue(null),
            'p_no' => $this->string(30)->defaultValue(''),
            'c_area' => $this->double()->defaultValue(0.0),
            'f_area' => $this->double()->defaultValue(0.0),
            'note' => $this->string(80)->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->execute('CREATE INDEX sidx_field_geom ON field USING GIST (geom)');
        $this->createIndex('ix_field_pno', '{{%field}}', 'p_no');
        $this->createIndex('ix_field_aza_id', '{{%field}}', 'aza_id');
        $this->createIndex('ix_field_c_area', '{{%field}}', 'c_area');
        $this->createIndex('ix_field_f_area', '{{%field}}', 'f_area');
        // 外部キー
        $this->addForeignKey('fk_field_aza_id_aza_id', '{{%field}}', 'aza_id', '{{%aza}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_created_by_user_id', '{{%field}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_updated_by_user_id', '{{%field}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%field_person}}', [
            'id' => $this->primaryKey(),
            'role' => $this->integer()->notNull()->defaultValue(1),
            'field_id' => $this->integer()->notNull(),
            'person_id' => $this->integer()->notNull(),
            'valid_from' => $this->date()->notNull(),
            'valid_to' => $this->date()->null()->defaultValue(null),
            'note' => $this->string(80)->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->createIndex('ix_field_person_role', '{{%field_person}}', 'role');
        $this->createIndex('ix_field_person_field_id', '{{%field_person}}', 'field_id');
        $this->createIndex('ix_field_person_person_id', '{{%field_person}}', 'person_id');
        $this->createIndex('ix_field_person_valid_from', '{{%field_person}}', 'valid_from');
        $this->createIndex('ix_field_person_valid_to', '{{%field_person}}', 'valid_to');
        // 外部キー
        $this->addForeignKey('fk_field_person_field_id_field_id', '{{%field_person}}', 'field_id', '{{%field}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_person_person_id_person_id', '{{%field_person}}', 'person_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_person_created_by_user_id', '{{%field_person}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_person_updated_by_user_id', '{{%field_person}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        // 制約 ... role, field_id が valid_to が null のときにユニーク
        $sql = <<< INDEX_SQL
CREATE UNIQUE INDEX field_person_current_unique
ON field_person (field_id, role)
WHERE valid_to IS NULL
INDEX_SQL;
        $this->execute($sql);

        $this->createTable('{{%field_usage}}', [
            'id' => $this->primaryKey(),
            'field_id' => $this->integer()->notNull(),
            'usage_id' => $this->integer()->notNull(),
            'valid_from' => $this->date()->notNull(),
            'valid_to' => $this->date()->null()->defaultValue(null),
            'note' => $this->string(80)->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->createIndex('ix_field_usage_field_id', '{{%field_usage}}', 'field_id');
        $this->createIndex('ix_field_usage_usage_id', '{{%field_usage}}', 'usage_id');
        $this->createIndex('ix_field_usage_valid_from', '{{%field_usage}}', 'valid_from');
        $this->createIndex('ix_field_usage_valid_to', '{{%field_usage}}', 'valid_to');
        // 外部キー
        $this->addForeignKey('fk_field_usage_field_id_field_id', '{{%field_usage}}', 'field_id', '{{%field}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_usage_usage_id_usage_id', '{{%field_usage}}', 'usage_id', '{{%usage}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_usage_created_by_user_id', '{{%field_usage}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_usage_updated_by_user_id', '{{%field_usage}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        // 制約 ... field_id が valid_to が null のときにユニーク
        $sql = <<< INDEX_SQL
CREATE UNIQUE INDEX field_usage_current_unique
ON field_usage (field_id)
WHERE valid_to IS NULL
INDEX_SQL;
        $this->execute($sql);

        // QGIS, qwc2 表示用ビュー
        $this->execute(<<< VIEW_SQL
CREATE VIEW v_field AS
SELECT
  f.id,
  f.geom,
  a.name AS aza,
  f.p_no,
  p1.name AS owner,
  p2.name AS cultivator,
  u.name AS usage,
  f.c_area,
  f.f_area as area,
  f.note
FROM field f
LEFT JOIN aza a ON f.aza_id = a.id
LEFT JOIN field_person fp1 ON fp1.field_id = f.id AND fp1.role = 1 AND fp1.valid_to IS null
LEFT JOIN person p1 ON p1.id = fp1.person_id
LEFT JOIN field_person fp2 ON fp2.field_id = f.id AND fp2.role = 2 AND fp2.valid_to IS null
LEFT JOIN person p2 ON p2.id = fp2.person_id
LEFT JOIN field_usage fu ON fu.field_id = f.id AND fu.valid_to IS null
LEFT JOIN usage u ON u.id = fu.usage_id
VIEW_SQL
        );

        $this->seedField();
        // $this->seedFieldsCsv();
    }

    public function seedField()
    {
        $rows = (new \yii\db\Query())
            ->select(['*'])
            ->from('isg.tanada')
            ->distinct()
            ->orderBy('id')
            ->all();
        foreach ($rows as $row) {
            $cols = [
                'geom' => $row['geom'],
                'c_area' => $row['area'],
                'f_area' => $row['area'],
            ];
            if ($row['p_no'] != '') {
                $cols['p_no'] = $row['p_no'];
            }
            $cols['aza_id'] = null;
            $this->insert('field', $cols);

            $field_id = (new \yii\db\Query())
                ->from('field')->max('id');

            if ($row['owner'] != '') {
                $owner = (new \yii\db\Query())
                    ->select(['person_id'])
                    ->from('person_work')
                    ->where(['src' => 1])
                    ->andWhere(['name' => $row['owner']])
                    ->one();
                $o_cols = [
                    'field_id' => $field_id,
                    'person_id' => (int)$owner['person_id'],
                    'role' => 1,
                    'valid_from' => '1900-01-01',
                ];
                $this->insert('field_person', $o_cols);
            }
            if ($row['cultivator'] != '') {
                $manager = (new \yii\db\Query())
                    ->select(['person_id'])
                    ->from('person_work')
                    ->where(['src' => 2])
                    ->andWhere(['name' => $row['cultivator']])
                    ->one();
                $m_cols = [
                    'field_id' => $field_id,
                    'person_id' => (int)$manager['person_id'],
                    'role' => 2,
                    'valid_from' => '1900-01-01',
                ];
                $this->insert('field_person', $m_cols);
            }
            $usage_text = $row['usage'];
            if ($usage_text == '') {
                $usage_text = '----';
            }
            $usage = (new \yii\db\Query())
                ->select(['id'])
                ->from('usage')
                ->where(['name' => $usage_text])
                ->one();
            $fu_cols = [
                'field_id' => $field_id,
                'usage_id' => (int)$usage['id'],
                'valid_from' => '1900-01-01',
            ];
            $this->insert('field_usage', $fu_cols);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP VIEW IF EXISTS v_field');
        $this->dropTable('{{%field_usage}}');
        $this->dropTable('{{%field_person}}');
        $this->dropTable('{{%field}}');
    }

}

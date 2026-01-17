<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%forest}}`.
 */
class m260113_005803_create_forest_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS postgis');

        $this->createTable('{{%forest}}', [
            'id' => $this->primaryKey(),
            'geom' => 'public.geometry(MultiPolygon,2447) NOT NULL',
            'p_no' => $this->string(30)->defaultValue(''),
            'aza_id' => $this->integer()->defaultValue(null),
            'type_id' => $this->integer()->defaultValue(null),
            'area' => $this->double()->defaultValue(0.0),
            'note' => $this->string(80)->defaultValue(''),
            'created_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'updated_at' => 'TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
        ]);
        // インデックス
        $this->execute('CREATE INDEX sidx_forest_geom ON forest USING GIST (geom)');
        $this->createIndex('ix_forest_pno', '{{%forest}}', 'p_no');
        $this->createIndex('ix_forest_aza_id', '{{%forest}}', 'aza_id');
        $this->createIndex('ix_forest_type_id', '{{%forest}}', 'type_id');
        $this->createIndex('ix_forest_area', '{{%forest}}', 'area');
        // 外部キー
        $this->addForeignKey('fk_forest_aza_id_aza_id', '{{%forest}}', 'aza_id', '{{%aza}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_type_id_frtype_id', '{{%forest}}', 'type_id', '{{%frtype}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_created_by_user_id', '{{%forest}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_updated_by_user_id', '{{%forest}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%forest_person}}', [
            'id' => $this->primaryKey(),
            'role' => $this->integer()->notNull()->defaultValue(1),
            'forest_id' => $this->integer()->notNull(),
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
        $this->createIndex('ix_forest_person_role', '{{%forest_person}}', 'role');
        $this->createIndex('ix_forest_person_forest_id', '{{%forest_person}}', 'forest_id');
        $this->createIndex('ix_forest_person_person_id', '{{%forest_person}}', 'person_id');
        $this->createIndex('ix_forest_person_valid_from', '{{%forest_person}}', 'valid_from');
        $this->createIndex('ix_forest_person_valid_to', '{{%forest_person}}', 'valid_to');
        // 外部キー
        $this->addForeignKey('fk_forest_person_forest_id_forest_id', '{{%forest_person}}', 'forest_id', '{{%forest}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_person_person_id_person_id', '{{%forest_person}}', 'person_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_person_created_by_user_id', '{{%forest_person}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_person_updated_by_user_id', '{{%forest_person}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        // 制約 ... role, forest_id が valid_to が null のときにユニーク
$sql = <<< INDEX_SQL
CREATE UNIQUE INDEX forest_person_current_unique
ON forest_person (forest_id, role)
WHERE valid_to IS NULL
INDEX_SQL;
        $this->execute($sql);

        // QGIS, qwc2 表示用ビュー
        $this->execute( <<< VIEW_SQL
CREATE VIEW v_forest AS
SELECT
  f.id,
  f.geom,
  a.name AS aza,
  f.p_no,
  t.name AS type,
  p1.name AS owner,
  p2.name AS manager,
  f.area,
  f.note
FROM forest f
LEFT JOIN aza a ON f.aza_id = a.id
LEFT JOIN frtype t ON f.type_id = t.id
LEFT JOIN forest_person fp1 ON fp1.forest_id = f.id AND fp1.role = 1 AND fp1.valid_to IS null
LEFT JOIN forest_person fp2 ON fp2.forest_id = f.id AND fp2.role = 2 AND fp2.valid_to IS null
LEFT JOIN person p1 ON p1.id = fp1.person_id
LEFT JOIN person p2 ON p2.id = fp2.person_id
VIEW_SQL
        );

        $this->seedForest();
    }
    
    public function seedForest()
    {
        $rows = (new \yii\db\Query())
            ->select(['*'])
            ->from('isg.forest')
            ->distinct()
            ->orderBy('id')
            ->all();
        foreach ($rows as $row) {
            $cols = [
                'geom' => $row['geom'],
                'area' => $row['area'],
            ];
            if ($row['p_no'] != '') {
                $cols['p_no'] = $row['p_no'];
            }
            if ($row['memo'] != '') {
                $cols['note'] = $row['memo'];
            }
            if ($row['ko_aza'] != '') {
                $aza = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('aza')
                    ->where(['name' => $row['ko_aza']])
                    ->one();
                $cols['aza_id'] = (int)$aza['id'];
            }
            if ($row['type'] != '') {
                $type = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('frtype')
                    ->where(['name' => $row['type']])
                    ->one();
                $cols['type_id'] = (int)$type['id'];
            }
            $this->insert('forest', $cols);

            $forest_id = (new \yii\db\Query())
                ->from('forest')->max('id');

            if ($row['owner'] != '') {
                $owner = (new \yii\db\Query())
                    ->select(['person_id'])
                    ->from('person_work')
                    ->where(['src' => 3])
                    ->andWhere(['name' => $row['owner']])
                    ->one();
                $o_cols = [
                    'forest_id' => $forest_id,
                    'person_id' => (int)$owner['person_id'],
                    'role' => 1,
                    'valid_from' => '1900-01-01',
                ];
                $this->insert('forest_person', $o_cols);
            }
            if ($row['manager'] != '') {
                $manager = (new \yii\db\Query())
                    ->select(['person_id'])
                    ->from('person_work')
                    ->where(['src' => 4])
                    ->andWhere(['name' => $row['manager']])
                    ->one();
                $m_cols = [
                    'forest_id' => $forest_id,
                    'person_id' => (int)$manager['person_id'],
                    'role' => 2,
                    'valid_from' => '1900-01-01',
                ];
                $this->insert('forest_person', $m_cols);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP VIEW IF EXISTS v_forest');
        $this->dropTable('{{%forest_person}}');
        $this->dropTable('{{%forest}}');
    }
}

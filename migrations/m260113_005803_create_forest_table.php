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
            'owner_id' => $this->integer()->defaultValue(null),
            'manager_id' => $this->integer()->defaultValue(null),
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
        $this->createIndex('ix_forest_owner_id', '{{%forest}}', 'owner_id');
        $this->createIndex('ix_forest_manager_id', '{{%forest}}', 'manager_id');
        $this->createIndex('ix_forest_area', '{{%forest}}', 'area');
        // 外部キー
        $this->addForeignKey('fk_forest_aza_id_aza_id', '{{%forest}}', 'aza_id', '{{%aza}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_type_id_frtype_id', '{{%forest}}', 'type_id', '{{%frtype}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_owner_id_person_id', '{{%forest}}', 'owner_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_manager_id_person_id', '{{%forest}}', 'manager_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_created_by_user_id', '{{%forest}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_updated_by_user_id', '{{%forest}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        // QGIS, qwc2 表示用ビュー
        $this->execute( <<< VIEW_SQL
CREATE VIEW v_forest AS
SELECT
  f.id,
  f.geom,
  f.p_no,
  a.name AS aza,
  t.name AS type,
  p1.name AS owner,
  p2.name AS manager,
  f.area,
  f.note
FROM forest f
LEFT JOIN aza a ON f.aza_id = a.id
LEFT JOIN frtype t ON f.type_id = t.id
LEFT JOIN person p1 ON f.owner_id = p1.id
LEFT JOIN person p2 ON f.manager_id = p2.id
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
            if ($row['owner'] != '') {
                $owner = (new \yii\db\Query())
                    ->select(['person_id'])
                    ->from('person_work')
                    ->where(['src' => 3])
                    ->andWhere(['name' => $row['owner']])
                    ->one();
                $cols['owner_id'] = (int)$owner['person_id'];
            }
            if ($row['manager'] != '') {
                $manager = (new \yii\db\Query())
                    ->select(['person_id'])
                    ->from('person_work')
                    ->where(['src' => 4])
                    ->andWhere(['name' => $row['manager']])
                    ->one();
                $cols['manager_id'] = (int)$manager['person_id'];
            }
            $this->insert('forest', $cols);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP VIEW IF EXISTS v_forest');
        $this->dropTable('{{%forest}}');
    }
}

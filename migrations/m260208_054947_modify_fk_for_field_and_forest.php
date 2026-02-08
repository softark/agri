<?php

use yii\db\Migration;

class m260208_054947_modify_fk_for_field_and_forest extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 外部キー field_person
        $this->dropForeignKey('fk_field_person_field_id_field_id', '{{%field_person}}');
        $this->dropForeignKey('fk_field_person_person_id_person_id', '{{%field_person}}');
        $this->addForeignKey('fk_field_person_field_id_field_id', '{{%field_person}}', 'field_id', '{{%field}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_field_person_person_id_person_id', '{{%field_person}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'RESTRICT');

        // 外部キー field_usage
        $this->dropForeignKey('fk_field_usage_field_id_field_id', '{{%field_usage}}');
        $this->dropForeignKey('fk_field_usage_usage_id_usage_id', '{{%field_usage}}');
        $this->addForeignKey('fk_field_usage_field_id_field_id', '{{%field_usage}}', 'field_id', '{{%field}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_field_usage_usage_id_usage_id', '{{%field_usage}}', 'usage_id', '{{%usage}}', 'id', 'CASCADE', 'RESTRICT');

        // 外部キー forest_person
        $this->dropForeignKey('fk_forest_person_forest_id_forest_id', '{{%forest_person}}');
        $this->dropForeignKey('fk_forest_person_person_id_person_id', '{{%forest_person}}');
        $this->addForeignKey('fk_forest_person_forest_id_forest_id', '{{%forest_person}}', 'forest_id', '{{%forest}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_forest_person_person_id_person_id', '{{%forest_person}}', 'person_id', '{{%person}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 外部キー field_person
        $this->dropForeignKey('fk_field_person_field_id_field_id', '{{%field_person}}');
        $this->dropForeignKey('fk_field_person_person_id_person_id', '{{%field_person}}');
        $this->addForeignKey('fk_field_person_field_id_field_id', '{{%field_person}}', 'field_id', '{{%field}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_person_person_id_person_id', '{{%field_person}}', 'person_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');

        // 外部キー field_usage
        $this->dropForeignKey('fk_field_usage_field_id_field_id', '{{%field_usage}}');
        $this->dropForeignKey('fk_field_usage_usage_id_usage_id', '{{%field_usage}}');
        $this->addForeignKey('fk_field_usage_field_id_field_id', '{{%field_usage}}', 'field_id', '{{%field}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_field_usage_usage_id_usage_id', '{{%field_usage}}', 'usage_id', '{{%usage}}', 'id', 'RESTRICT', 'RESTRICT');

        // 外部キー forest_person
        $this->dropForeignKey('fk_forest_person_forest_id_forest_id', '{{%forest_person}}');
        $this->dropForeignKey('fk_forest_person_person_id_person_id', '{{%forest_person}}');
        $this->addForeignKey('fk_forest_person_forest_id_forest_id', '{{%forest_person}}', 'forest_id', '{{%forest}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_forest_person_person_id_person_id', '{{%forest_person}}', 'person_id', '{{%person}}', 'id', 'RESTRICT', 'RESTRICT');
    }
}

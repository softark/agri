<?php

use yii\db\Migration;

class m260206_045855_create_field_rel_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $rows = (new \yii\db\Query())->select(['field_id', 'person_id'])->from('field_person')
            ->where(['and',
                ['role' => 2],
                ['valid_to' => null],
            ])->all();
        foreach ($rows as $row) {
            if ((new \yii\db\Query())->select('id')->from('field_person')
                    ->where(['and',
                        ['role' => 1],
                        ['field_id' => $row['field_id']],
                        ['person_id' => 30], // 木原加津也
                        ['valid_to' => null],
                    ])->count() != 0) {
                $this->insert('field_person',
                    [
                        'field_id' => $row['field_id'],
                        'person_id' => 30,
                        'role' => 3,
                        'valid_from' => '1900-01-01',
                        'valid_to' => null,
                    ]);
            } else {
                $this->insert('field_person',
                    [
                        'field_id' => $row['field_id'],
                        'person_id' => $row['person_id'],
                        'role' => 3,
                        'valid_from' => '1900-01-01',
                        'valid_to' => null,
                    ]);
            }
            $this->insert('field_person',
                [
                    'field_id' => $row['field_id'],
                    'person_id' => $row['person_id'],
                    'role' => 4,
                    'valid_from' => '1900-01-01',
                    'valid_to' => null,
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public
    function safeDown()
    {
        $this->delete('field_person', 'role > 2');
    }
}

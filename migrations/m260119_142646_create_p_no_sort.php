<?php

use yii\db\Migration;

class m260119_142646_create_p_no_sort extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<< SQL
ALTER TABLE forest
ADD COLUMN p_no_sort integer
GENERATED ALWAYS AS (
    (
      (regexp_match(p_no, '^\d+'))[1]::int * 1000
      +
      COALESCE(
        (regexp_match(p_no, '^\d+-(\d+)'))[1]::int,
        0
      )
    )
) STORED;
SQL;
        $this->execute($sql);

        $sql = <<< SQL
CREATE INDEX idx_forest_p_no_sort
ON forest (p_no_sort);
SQL;
        $this->execute($sql);

        $sql = <<< SQL
ALTER TABLE field
ADD COLUMN p_no_sort integer
GENERATED ALWAYS AS (
    (
      (regexp_match(p_no, '^\d+'))[1]::int * 1000
      +
      COALESCE(
        (regexp_match(p_no, '^\d+-(\d+)'))[1]::int,
        0
      )
    )
) STORED;
SQL;
        $this->execute($sql);

        $sql = <<< SQL
CREATE INDEX idx_field_p_no_sort
ON field (p_no_sort);
SQL;
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = <<< SQL
DROP INDEX idx_forest_p_no_sort;
SQL;
        $this->execute($sql);

        $sql = <<< SQL
ALTER TABLE forest
DROP COLUMN p_no_sort;
SQL;
        $this->execute($sql);

        $sql = <<< SQL
DROP INDEX idx_field_p_no_sort;
SQL;
        $this->execute($sql);

        $sql = <<< SQL
ALTER TABLE field
DROP COLUMN p_no_sort;
SQL;
        $this->execute($sql);
    }
}

<?php

use yii\db\Migration;

class m260207_104640_setup_area_calc_tricks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 農地面積の自動計算
        $this->execute("
CREATE FUNCTION update_area_of_field() RETURNS trigger AS $$
BEGIN
  NEW.c_area := public.ST_Area(NEW.geom);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
");
        // 農地のトリガ
        $this->execute("
CREATE TRIGGER trg_update_area_of_field
BEFORE INSERT OR UPDATE ON field
FOR EACH ROW EXECUTE FUNCTION update_area_of_field();
");

        // 山林面積の自動計算
        $this->execute("
CREATE FUNCTION update_area_of_forest() RETURNS trigger AS $$
BEGIN
  NEW.area := public.ST_Area(NEW.geom);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
");
        // 山林のトリガ
        $this->execute("
CREATE TRIGGER trg_update_area_of_forest
BEFORE INSERT OR UPDATE ON forest
FOR EACH ROW EXECUTE FUNCTION update_area_of_forest();
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
DROP TRIGGER trg_update_area_of_field on field;
");
        $this->execute("
DROP FUNCTION update_area_of_field();
");
        $this->execute("
DROP TRIGGER trg_update_area_of_forest on forest;
");
        $this->execute("
DROP FUNCTION update_area_of_forest();
");
    }
}

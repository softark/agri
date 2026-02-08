<?php

use yii\db\Migration;

class m260208_081041_setup_forest_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<< TRIGGER_FUNC_SQL
CREATE OR REPLACE FUNCTION trg_v_forest_edit()
RETURNS trigger
LANGUAGE plpgsql
AS $$
DECLARE r record;
BEGIN
  IF TG_OP = 'INSERT' THEN
    INSERT INTO agri.forest (geom, p_no)
    VALUES (NEW.geom, NEW.p_no)
    RETURNING id, geom, p_no INTO r;
  
    NEW.id := r.id;
    NEW.geom := r.geom;
    NEW.p_no := r.p_no;
    
    RETURN NEW;

  ELSIF TG_OP = 'UPDATE' THEN
    UPDATE agri.forest
       SET geom = NEW.geom,
           p_no  = NEW.p_no
    WHERE id = OLD.id
    RETURNING id, geom, p_no INTO r;
  
    NEW.id := r.id;
    NEW.geom := r.geom;
    NEW.p_no := r.p_no;

    RETURN NEW;

  ELSIF TG_OP = 'DELETE' THEN
    DELETE FROM agri.forest
    WHERE id = OLD.id;

    RETURN OLD;
  END IF;

  RETURN NULL;
END;
$$;
TRIGGER_FUNC_SQL
        );

        $this->execute(<<< TRIGGER_SQL
CREATE TRIGGER v_forest_edit
INSTEAD OF INSERT OR UPDATE OR DELETE
ON v_forest
FOR EACH ROW
EXECUTE FUNCTION trg_v_forest_edit();
TRIGGER_SQL
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // QGIS, qwc2 表示用ビュー
        $this->execute(<<< TRIGGER_SQL
DROP TRIGGER v_forest_edit ON v_forest;
TRIGGER_SQL
        );

        $this->execute(<<< FUNC_SQL
DROP FUNCTION trg_v_forest_edit();
FUNC_SQL
        );
    }
}

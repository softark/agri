<?php

use yii\db\Migration;

class m260206_114520_setup_field_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // QGIS, qwc2 表示用ビュー
        $this->execute('DROP VIEW IF EXISTS v_field');
        $this->execute(<<< VIEW_SQL
CREATE VIEW v_field AS
SELECT
  f.id,
  f.geom,
  a.name AS aza,
  f.p_no,
  p1.name AS owner,
  p2.name AS cultivator,
  p3.name AS chusankan,
  p4.name AS saimokusho,
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
LEFT JOIN field_person fp3 ON fp3.field_id = f.id AND fp3.role = 3 AND fp3.valid_to IS null
LEFT JOIN person p3 ON p3.id = fp3.person_id
LEFT JOIN field_person fp4 ON fp4.field_id = f.id AND fp4.role = 4 AND fp4.valid_to IS null
LEFT JOIN person p4 ON p4.id = fp4.person_id
LEFT JOIN field_usage fu ON fu.field_id = f.id AND fu.valid_to IS null
LEFT JOIN usage u ON u.id = fu.usage_id
VIEW_SQL
        );

        $this->execute(<<< TRIGGER_FUNC_SQL
CREATE OR REPLACE FUNCTION trg_v_field_edit()
RETURNS trigger
LANGUAGE plpgsql
AS $$
DECLARE r record;
BEGIN
  IF TG_OP = 'INSERT' THEN
    INSERT INTO agri.field (geom, p_no)
    VALUES (NEW.geom, NEW.p_no)
    RETURNING id, geom, p_no INTO r;
  
    NEW.id := r.id;
    NEW.geom := r.geom;
    NEW.p_no := r.p_no;
    
    RETURN NEW;

  ELSIF TG_OP = 'UPDATE' THEN
    UPDATE agri.field
       SET geom = NEW.geom,
           p_no  = NEW.p_no
    WHERE id = OLD.id
    RETURNING id, geom, p_no INTO r;
  
    NEW.id := r.id;
    NEW.geom := r.geom;
    NEW.p_no := r.p_no;

    RETURN NEW;

  ELSIF TG_OP = 'DELETE' THEN
    DELETE FROM argi.field
    WHERE id = OLD.id;

    RETURN OLD;
  END IF;

  RETURN NULL;
END;
$$;
TRIGGER_FUNC_SQL
        );

        $this->execute(<<< TRIGGER_SQL
CREATE TRIGGER v_field_edit
INSTEAD OF INSERT OR UPDATE OR DELETE
ON v_field
FOR EACH ROW
EXECUTE FUNCTION trg_v_field_edit();
TRIGGER_SQL
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // QGIS, qwc2 表示用ビュー
        $this->execute('DROP VIEW IF EXISTS v_field');
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
    }

}

<?php

use yii\db\Migration;

class m260212_152511_extend_field_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // bbox, center のカラムを追加
        $this->addColumn('agri.field', 'bbox_3857', 'public.geometry(Polygon,3857)');
        $this->addColumn('agri.field', 'center_3857', 'public.geometry(Point,3857)');
        // 初期値投入
        $this->execute("
UPDATE agri.field
SET
  bbox_3857 = public.ST_Envelope(public.ST_Transform(geom, 3857)),
  center_3857 = public.ST_PointOnSurface(public.ST_Transform(geom, 3857))
WHERE geom IS NOT NULL;
");

        // 農地トリガー関数を拡張修正
        $this->execute("
CREATE OR REPLACE FUNCTION update_area_of_field() RETURNS trigger AS $$
BEGIN
  NEW.c_area := public.ST_Area(NEW.geom);
  NEW.bbox_3857 := public.ST_Envelope(public.ST_Transform(NEW.geom, 3857));
  NEW.center_3857 := public.ST_PointOnSurface(public.ST_Transform(NEW.geom, 3857));
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 農地のトリガ関数を元に戻す
        $this->execute("
CREATE OR REPLACE FUNCTION update_area_of_field() RETURNS trigger AS $$
BEGIN
  NEW.c_area := public.ST_Area(NEW.geom);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
");
        // 追加した columns を落とす
        $this->dropColumn('agri.field', 'bbox_3857');
        $this->dropColumn('agri.field', 'center_3857');
    }

}

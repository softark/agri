--
-- common.log_cache_invalidation()
--

DECLARE
bbox public.geometry;
  layer text;
  buff_x double precision;
  buff_y double precision;
  bbox_3857_old public.geometry;
  bbox_3857_new public.geometry;
BEGIN

  FOREACH layer IN ARRAY TG_ARGV LOOP

    -- バッファ設定を取得
SELECT buff_east_west, buff_north_south
INTO buff_x, buff_y
FROM common.cache_config WHERE layer_name = layer;

IF buff_x IS NULL THEN
      RAISE WARNING 'No buffer config for layer %, using default values', layer;
      buff_x := 10;
	  buff_y := 10;
END IF;

    bbox_3857_old := NULL;
    bbox_3857_new := NULL;

    -- bbox を取得 /（EPSG:6673）→ EPSG:3857 に変換
    IF TG_OP = 'DELETE' THEN
	  bbox_3857_old := public.ST_Transform(OLD.geom, 3857);
      -- bbox を拡大
	  bbox_3857_old := public.ST_Expand(bbox_3857_old, buff_x, buff_y);
    ELSEIF TG_OP = 'INSERT' THEN
	  bbox_3857_new := public.ST_Transform(NEW.geom, 3857);
      -- bbox を拡大
	  bbox_3857_new := public.ST_Expand(bbox_3857_new, buff_x, buff_y);
ELSE
	  -- UPDATE
	  bbox_3857_old := public.ST_Transform(OLD.geom, 3857);
      -- bbox を拡大
	  bbox_3857_old := public.ST_Expand(bbox_3857_old, buff_x, buff_y);
	  bbox_3857_new := public.ST_Transform(NEW.geom, 3857);
      -- bbox を拡大
	  bbox_3857_new := public.ST_Expand(bbox_3857_new, buff_x, buff_y);
	  IF public.ST_Distance(bbox_3857_old, bbox_3857_new) < 1.0 THEN
	    bbox_3857_old := public.ST_Union(bbox_3857_old, bbox_3857_new);
		bbox_3857_new := NULL;
END IF;
END IF;

    -- BBOX を記録
    IF bbox_3857_old IS NOT NULL THEN
      INSERT INTO common.cache_invalidation_log (
        layer_name,
        bbox_xmin, bbox_ymin, bbox_xmax, bbox_ymax
      )
SELECT
    layer,
    public.ST_XMin(bbox_3857_old),
    public.ST_YMin(bbox_3857_old),
    public.ST_XMax(bbox_3857_old),
    public.ST_YMax(bbox_3857_old);
END IF;

    IF bbox_3857_new IS NOT NULL THEN
      INSERT INTO common.cache_invalidation_log (
        layer_name,
        bbox_xmin, bbox_ymin, bbox_xmax, bbox_ymax
      )
SELECT
    layer,
    public.ST_XMin(bbox_3857_new),
    public.ST_YMin(bbox_3857_new),
    public.ST_XMax(bbox_3857_new),
    public.ST_YMax(bbox_3857_new);
END IF;

    PERFORM pg_notify('cache_invalidation', layer);
    PERFORM pg_notify('cache_invalidation_vm', layer);

END LOOP;

RETURN NEW;
END;

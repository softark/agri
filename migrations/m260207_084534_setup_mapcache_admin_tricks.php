<?php

use yii\db\Migration;

class m260207_084534_setup_mapcache_admin_tricks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // キャッシュ無効化トリガー field
        $this->execute("
CREATE TRIGGER trg_cache_log_field
AFTER INSERT OR UPDATE OR DELETE ON field
FOR EACH ROW
EXECUTE FUNCTION common.log_cache_invalidation('agri','tanada');
");

        // キャッシュ無効化トリガー forest
        $this->execute("
CREATE TRIGGER trg_cache_log_forest
AFTER INSERT OR UPDATE OR DELETE ON forest
FOR EACH ROW
EXECUTE FUNCTION common.log_cache_invalidation('forest');
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
DROP TRIGGER trg_cache_log_field on field;
");
        $this->execute("
DROP TRIGGER trg_cache_log_forest on forest;
");
    }
}

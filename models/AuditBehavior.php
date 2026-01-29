<?php

namespace app\models;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\components\Audit;

/**
 * ActiveRecord の CRUD を監査ログに出す Behavior。
 *
 * - insert/update/delete を記録
 * - update は changed columns（カラム名）だけ残す（値は残さない）
 * - 監査対象外カラム（updated_at 等）は除外可能
 */
class AuditBehavior extends Behavior
{
    /**
     * 監査ログ上の model 名（未指定ならクラス名/テーブル名から推測）
     * 例: 'agri_forest'
     */
    public ?string $modelName = null;

    /**
     * update 時に「変更があっても監査対象から除外する」カラム
     * 例: updated_at, updated_by など
     */
    public array $ignoreAttributes = ['updated_at', 'updated_by', 'created_at', 'created_by'];

    /**
     * create/update/delete の event 名プレフィックス
     * 例: 'ar' => 'ar.create' など
     */
    public string $eventPrefix = 'ar';

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function afterInsert($event): void
    {
        $owner = $this->owner;
        Audit::log($this->eventPrefix . '.create', [
            'model' => $this->resolveModelName($owner),
            'model_id' => $this->resolvePk($owner),
        ]);
    }

    public function afterUpdate($event): void
    {
        $owner = $this->owner;

        // changed attributes: ["col" => oldValue, ...]
        $changed = is_array($event->changedAttributes ?? null) ? array_keys($event->changedAttributes) : [];

        // 監査対象外を除外
        $changed = array_values(array_diff($changed, $this->ignoreAttributes));

        // 「意味のある変更がない update」はログを出さない（好みで）
        if (count($changed) === 0) {
            return;
        }

        Audit::log($this->eventPrefix . '.update', [
            'model' => $this->resolveModelName($owner),
            'model_id' => $this->resolvePk($owner),
            'changed_columns' => $changed,
        ]);
    }

    public function afterDelete($event): void
    {
        $owner = $this->owner;

        Audit::log($this->eventPrefix . '.delete', [
            'model' => $this->resolveModelName($owner),
            'model_id' => $this->resolvePk($owner),
        ]);
    }

    private function resolveModelName(ActiveRecord $owner): string
    {
        if (!empty($this->modelName)) {
            return $this->modelName;
        }

        // デフォルト：テーブル名（schema を含む場合はそのまま）
        try {
            return (string)$owner::tableName();
        } catch (\Throwable $e) {
            // 最後の手段：クラス名
            return (new \ReflectionClass($owner))->getShortName();
        }
    }

    /**
     * PK が複合の場合は JSON 文字列で返す（ログ上は文字列1本にしておく）
     */
    private function resolvePk(ActiveRecord $owner): ?string
    {
        $pk = $owner->getPrimaryKey(true); // ['id' => 123] or ['a'=>1,'b'=>2]
        if ($pk === null || $pk === []) {
            return null;
        }
        if (count($pk) === 1) {
            return (string)array_values($pk)[0];
        }
        return json_encode($pk, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

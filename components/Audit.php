<?php
namespace app\components;

use Yii;
use yii\helpers\Json;

final class Audit
{
    public const CATEGORY = 'audit';

    /**
     * 監査ログを1行(JSON)で出す。
     *
     * @param string $event 例: "agri/forest.update", "web/person.view"
     * @param array  $data  追加情報（model, model_id, changed_columns等）
     * @param string $level info|warning|error
     */
    public static function log(string $event, array $data = [], string $level = 'info'): void
    {
        $req  = Yii::$app->getRequest();
        $user = Yii::$app->has('user', true) ? Yii::$app->getUser() : null;

        // 監査ログの共通フィールド
        $payload = array_merge([
            'ts'       => gmdate('c'), // UTC ISO8601。JSTで欲しければ date('c') に変更
            'event'    => $event,
            'user_id'  => ($user && !$user->getIsGuest()) ? (string)$user->getId() : null,
            'ip'       => $req ? $req->getUserIP() : null,
            'method'   => $req ? $req->getMethod() : null,
            'path'     => $req ? $req->getPathInfo() : null,
            'route'    => Yii::$app->requestedRoute ?? null,
            // ここは「入れると危ない」ことが多いので、必要になったら絞って入れる
            // 'query'  => $req ? $req->getQueryParams() : null,
        ], $data);

        // JSONを1行にする（JSONL）
        $line = Json::encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // レベル別に Yii logger へ
        switch ($level) {
            case 'error':
                Yii::error($line, self::CATEGORY);
                break;
            case 'warning':
                Yii::warning($line, self::CATEGORY);
                break;
            default:
                Yii::info($line, self::CATEGORY);
                break;
        }
    }
}

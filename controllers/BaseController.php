<?php
namespace app\controllers;

use yii\web\Controller;
use app\components\Audit;

class BaseController extends Controller
{
    /**
     * @var array|string[] 監査から除外することローラ・アクション
     */
    protected array $auditExclude = [
        'site/error',
    ];

    public function beforeAction($action)
    {
        // 監査：アクセス試行
        if (!in_array($this->route, $this->auditExclude, true)) {
            Audit::log('ctrl.access', [
                'controller' => $this->id,
                'action' => $action->id,
                'route' => $this->route,
            ]);
        }

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        if (!in_array($this->route, $this->auditExclude, true)) {
            // 監査：結果（成功/失敗）
            Audit::log('ctrl.done', [
                'controller' => $this->id,
                'action' => $action->id,
                'route' => $this->route,
                'status' => \Yii::$app->response->statusCode,
            ]);
        }

        return parent::afterAction($action, $result);
    }
}

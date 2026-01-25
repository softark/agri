<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

trait LoadParamsTrait
{
    protected function rememberGridParams(
        array   $params,
        string  $pageParam,
        string  $sortParam,
        ?string $sessionKey = null
    ): array
    {
        $session = Yii::$app->session;

        $sessionKey = $sessionKey ?? implode(':', [Yii::$app->controller->id, Yii::$app->controller->action->id]);

        $formName = $this->formName();
        $keyBase = implode(':', [
                'gridState',
                $sessionKey,
                static::class,
                $formName,
            ]);

        $keyFilter = $keyBase . ':filter';
        $keyView = $keyBase . ':view';

        // 1) 今回来たものを抽出
        $hasFilter = array_key_exists($formName, $params);
        $hasPage = array_key_exists($pageParam, $params);
        $hasSort = array_key_exists($sortParam, $params);

        $storedFilter = $session->get($keyFilter, []);
        $storedView = $session->get($keyView, []);

        // 2) 検索条件が来たら filter 更新、page はリセット
        if ($hasFilter) {
            $storedFilter = [$formName => $params[$formName]];
            $session->set($keyFilter, $storedFilter);

            // 検索条件が変わったらページは1に戻す（view state から page を捨てる）
            unset($storedView[$pageParam]);
            // sort は維持したければ残す／検索条件と一緒にリセットしたければ unset
            // unset($storedView[$sortParam]);

            // 今回指定された page/sort があればそれは採用
            if ($hasPage) $storedView[$pageParam] = $params[$pageParam];
            if ($hasSort) $storedView[$sortParam] = $params[$sortParam];
            $session->set($keyView, $storedView);

            return ArrayHelper::merge($params, $storedFilter, $storedView);
        }

        // 3) filter 無しで page/sort だけ来たら、filter を補完して view 更新
        if ($hasPage || $hasSort) {
            if ($hasPage) $storedView[$pageParam] = $params[$pageParam];
            if ($hasSort) $storedView[$sortParam] = $params[$sortParam];
            $session->set($keyView, $storedView);

            return ArrayHelper::merge($params, $storedFilter, $storedView);
        }

        // 4) 何も来てないなら全部復元
        return ArrayHelper::merge($params, $storedFilter, $storedView);
    }
}
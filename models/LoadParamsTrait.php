<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

trait LoadParamsTrait
{
    /**
     * @param $searchModel Model
     * @param $dataProvider ActiveDataProvider
     * @param $params array|false|null
     */
    public function loadAndRememberParams(Model $searchModel, ActiveDataProvider $dataProvider, $params)
    {
        if (!is_array($params)) {
            return;
        }

        $key = $searchModel->formName();

//        if (!isset($params[$key])) {
//            $attrs = $searchModel->activeAttributes();
//            foreach($attrs as $attribute) {
//                if (!empty($searchModel->$attribute)) {
//                    $params[$key][$attribute] = $searchModel->$attribute;
//                }
//            }
//        }

        $recall = false;

        $session = Yii::$app->session;
        $session->open();

        if (!empty($key)) {
            if (!isset($params[$key])) {
                if (isset($session[$key]) && !is_array($session[$key])) {
                    $params[$key] = json_decode($session[$key], true);
                    $recall = true;
                }
            } else {
                $session[$key] = json_encode($params[$key]);
            }
        } else {
            $key = (new \ReflectionClass($this))->getShortName();
            if (count($params) == 0) {
                if (isset($session[$key]) && !is_array($session[$key])) {
                    $params = json_decode($session[$key], true);
                    $recall = true;
                }
            } else {
                $session[$key] = json_encode($params);
            }
        }

        $searchModel->load($params);

        $sortParam = $dataProvider->sort->sortParam;
        if (!isset($params[$sortParam])) {
            if (isset($session[$key . $sortParam])) {
                $params[$sortParam] = $session[$key . $sortParam];
                $dataProvider->sort->params = $params;
            }
        } else {
            $session[$key . $sortParam] = $params[$sortParam];
        }

        $pageParam = $dataProvider->pagination->pageParam;
        if (!isset($params[$pageParam])) {
            if (isset($session[$key . $pageParam]) && $recall) {
                Yii::$app->request->setQueryParams([$pageParam => $session[$key . $pageParam]]);
                // $dataProvider->pagination->page = intval($session[$key . $pageParam]) - 1;
            }
        } else {
            $session[$key . $pageParam] = $params[$pageParam];
        }

        $session->close();
    }
}
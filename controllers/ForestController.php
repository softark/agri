<?php

namespace app\controllers;

use app\models\Forest;
use app\models\ForestPerson;
use app\models\ForestSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ForestController implements the CRUD actions for Forest model.
 */
class ForestController extends Controller
{
    /**
     * Lists all Forest models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ForestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Displays a single Forest model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing Forest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $ret_route = null)
    {
        $model = $this->findModel($id);
        if ($ret_route == null) {
            $ret_route = ['index'];
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect($ret_route);
        }

        return $this->render('update', [
            'model' => $model,
            'ret_route' => $ret_route,
        ]);
    }

    /**
     * Finds the Forest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Forest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Forest::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateForestPerson($id)
    {
        $model = ForestPerson::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $ret_route = ['view', 'id' => $model->forest->id];

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect($ret_route);
        }

        return $this->render('update-forest-person', [
            'model' => $model,
            'forest' => $model->forest,
            'ret_route' => $ret_route,
        ]);
    }
    public function actionAddForestPerson($id, $role)
    {
        $forest = $this->findModel($id);
        $model = new ForestPerson();
        $model->forest_id = $forest->id;
        $model->role = $role;

        $ret_route = ['view', 'id' => $id];

        if ($this->request->isPost && $model->load($this->request->post()) && $model->addHistory($forest)) {
            return $this->redirect($ret_route);
        }

        return $this->render('add-forest-person', [
            'model' => $model,
            'forest' => $forest,
            'ret_route' => $ret_route,
        ]);
    }
}

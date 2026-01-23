<?php

namespace app\controllers;

use app\models\Forest;
use app\models\ForestExcel;
use app\models\ForestForm;
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
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'export' => ['POST'],
                    ],
                ],
            ]
        );
    }

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
     * Export to Excel
     */
    public function actionExport()
    {
        $searchModel = new ForestSearch();
        $dataProvider = $searchModel->search([], 0);
        if ($dataProvider->getCount() == 0) {
            return $this->goBack();
        }

        ForestExcel::exportForestList($dataProvider);
        return null;
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
    public function actionUpdate($id, $mode = null, $ret_route = null)
    {
        if ($ret_route == null) {
            $ret_route = ['index'];
        }

        $model = new ForestForm();
        $model->loadModels($id);

        if ($this->request->isPost) {
            $ret =$model->loadPost($mode, $this->request->post());
            if ($ret) {
                $ret = $model->saveModels($mode);
                if ($ret) {
                    if ($mode == 'o' || $mode == 'm') {
                        return $this->redirect(['update', 'id' => $id, 'ret_route' => $ret_route]);
                    }
                    return $this->redirect($ret_route);
                }
            }
        }

        return $this->render('update_ex', [
            'model' => $model,
            'ret_route' => $ret_route,
        ]);

        /*
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
        */
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->addHistory()) {
            return $this->redirect($ret_route);
        }

        return $this->render('add-forest-person', [
            'model' => $model,
            'forest' => $forest,
            'ret_route' => $ret_route,
        ]);
    }
}

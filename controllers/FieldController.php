<?php

namespace app\controllers;

use app\models\Field;
use app\models\FieldForm;
use app\models\FieldPerson;
use app\models\FieldSearch;
use app\models\FieldUsage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FieldController implements the CRUD actions for Field model.
 */
class FieldController extends Controller
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
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Field models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FieldSearch();
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
     * Displays a single Field model.
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
     * Updates an existing Field model.
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

        $model = new FieldForm();
        $model->loadModels($id);

        if ($this->request->isPost) {
            $ret =$model->loadPost($mode, $this->request->post());
            if ($ret) {
                $ret = $model->saveModels($mode);
                if ($ret) {
                    if ($mode == 'o' || $mode == 'c' || $mode == 'u') {
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
     * Finds the Field model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Field the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Field::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateFieldPerson($id)
    {
        $model = FieldPerson::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $ret_route = ['view', 'id' => $model->field->id];

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect($ret_route);
        }

        return $this->render('update-field-person', [
            'model' => $model,
            'field' => $model->field,
            'ret_route' => $ret_route,
        ]);
    }
    public function actionAddFieldPerson($id, $role)
    {
        $field = $this->findModel($id);
        $model = new FieldPerson();
        $model->field_id = $field->id;
        $model->role = $role;

        $ret_route = ['view', 'id' => $id];

        if ($this->request->isPost && $model->load($this->request->post()) && $model->addHistory()) {
            return $this->redirect($ret_route);
        }

        return $this->render('add-field-person', [
            'model' => $model,
            'field' => $field,
            'ret_route' => $ret_route,
        ]);
    }

    public function actionUpdateFieldUsage($id)
    {
        $model = FieldUsage::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $ret_route = ['view', 'id' => $model->field->id];

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect($ret_route);
        }

        return $this->render('update-field-usage', [
            'model' => $model,
            'field' => $model->field,
            'ret_route' => $ret_route,
        ]);
    }
    public function actionAddFieldUsage($id)
    {
        $field = $this->findModel($id);
        $model = new FieldUsage();
        $model->field_id = $field->id;

        $ret_route = ['view', 'id' => $id];

        if ($this->request->isPost && $model->load($this->request->post()) && $model->addHistory()) {
            return $this->redirect($ret_route);
        }

        return $this->render('add-field-usage', [
            'model' => $model,
            'field' => $field,
            'ret_route' => $ret_route,
        ]);
    }
}

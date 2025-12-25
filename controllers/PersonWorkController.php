<?php

namespace app\controllers;

use app\models\PersonWork;
use app\models\PersonWorkSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PersonWorkController implements the CRUD actions for PersonWork model.
 */
class PersonWorkController extends Controller
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
                        'import-tanada' => ['POST'],
                        'import-forest' => ['POST'],
                        'add-link' => ['POST'],
                        'delete-link' => ['POST'],
                        'init' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all PersonWork models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PersonWorkSearch();
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
     * Displays a single PersonWork model.
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
     * Deletes an existing PersonWork model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PersonWork model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PersonWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PersonWork::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionImportTanada()
    {
        $count = PersonWork::importFromTanada();
        if ($count > 0) {
            Yii::$app->session->setFlash('success', "$count 件の住所録辞書エントリを追加しました。");
        } else {
            Yii::$app->session->setFlash('warning', "0 件の住所録辞書エントリを追加しました。");
        }
        return $this->redirect(['index']);
    }

    public function actionImportForest()
    {
        $count = PersonWork::importFromForest();
        if ($count > 0) {
            Yii::$app->session->setFlash('success', "$count 件の住所録辞書エントリを追加しました。");
        } else {
            Yii::$app->session->setFlash('warning', "0 件の住所録辞書エントリを追加しました。");
        }
        return $this->redirect(['index']);
    }

    public function actionInit()
    {
        PersonWork::deleteAll();
        Yii::$app->session->setFlash('success', '住所録辞書を初期化しました。');
        return $this->redirect(['index']);
    }

    public function actionAddLink($id, $person_id)
    {
        $model = $this->findModel($id);
        $model->person_id = $person_id;
        $model->save();
        // return $this->redirect(['view', 'id' => $id]);
        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    public function actionDeleteLink($id)
    {
        $model = $this->findModel($id);
        $model->person_id = null;
        $model->save();
        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }
}

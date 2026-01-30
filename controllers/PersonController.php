<?php

namespace app\controllers;

use app\models\Contact;
use app\models\FieldSearch;
use app\models\ForestSearch;
use app\models\Person;
use app\models\PersonExcel;
use app\models\PersonForm;
use app\models\PersonRelation;
use app\models\PersonSearch;
use app\models\PersonWork;
use Yii;
use yii\base\UserException;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PersonController implements the CRUD actions for Person model.
 */
class PersonController extends BaseController
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
                        'reorder-contact' => ['POST'],
                        'delete-contact' => ['POST'],
                        'export' => ['POST'],
                        'delete-relation' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Person models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PersonSearch();
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
        $searchModel = new PersonSearch();
        $dataProvider = $searchModel->search([], 0, 'person:index');
        if ($dataProvider->getCount() == 0) {
            return $this->goBack();
        }

        PersonExcel::exportPersonList($dataProvider);
        return null;
    }

    public function actionSelect()
    {
        if (Yii::$app->request->isPjax) {
            $searchModel = new PersonSearch(['_form_name' => 'psel']);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 10, 'person:select');
            return $this->renderPartial('_select', [
                'dataProvider' => $dataProvider,
            ]);
        }
        throw new BadRequestHttpException();
    }

    /**
     * Displays a single Person model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $fieldParams = ArrayHelper::merge(['FieldSearch' => ['search_name' => $model->name]], Yii::$app->request->queryParams);
        $forestParams = ArrayHelper::merge(['ForestSearch' => ['search_name' => $model->name]], Yii::$app->request->queryParams);
        $fieldDp = (new FieldSearch())->search($fieldParams);
        $forestDp = (new ForestSearch())->search($forestParams);
        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('view', [
                'model' => $model,
                'fieldDp' => $fieldDp,
                'forestDp' => $forestDp,
            ]);
        } else {
            return $this->render('view', [
                'model' => $model,
                'fieldDp' => $fieldDp,
                'forestDp' => $forestDp,
            ]);
        }
    }

    /**
     * Creates a new Person model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $work_id
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $ret_route = ['index'];
        $model = new PersonForm();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->validate()) {
                    $model->savePersonAndContact();
                    return $this->redirect($ret_route);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'ret_route' => $ret_route,
        ]);
    }

    /**
     * Updates an existing Person model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $ret_route = null)
    {
        if ($ret_route === null) {
            $ret_route = ['index'];
        }
        $model = new PersonForm();
        $model->loadPersonAndContact($id);
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->validate()) {
                    $model->savePersonAndContact();
                    return $this->redirect($ret_route);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'ret_route' => $ret_route,
        ]);
    }

    /**
     * Deletes an existing Person model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('person.delete')) {
            throw new ForbiddenHttpException('この名簿を削除する権限がありません。');
        }

        /* @var Person $person */
        $person = $this->findModel($id);

        try {
            $person->delete();
        } catch (IntegrityException $e) {
            throw new UserException('この名簿を参照しているデータが存在するため、削除することが出来ません。');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested person does not exist.');
    }

    public function actionCreateContact($id)
    {
        $model = $this->findModel($id);
        $contact = new Contact();
        $contact->person_id = $model->id;
        $contact->order = count($model->contacts) + 1;

        if ($this->request->isPost && $contact->load($this->request->post()) && $contact->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('create-contact', [
            'model' => $model,
            'contact' => $contact,
        ]);
    }

    public function actionUpdateContact($id, $contact_id)
    {
        $model = $this->findModel($id);
        $contact = Contact::findOne($contact_id);
        if ($contact === null) {
            throw new NotFoundHttpException('The requested contact does not exist.');
        }

        if ($this->request->isPost && $contact->load($this->request->post()) && $contact->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update-contact', [
            'model' => $model,
            'contact' => $contact,
        ]);
    }

    public function actionReorderContact($id)
    {
        $contact_id = $this->request->post('contact_id');
        $direction = $this->request->post('direction');
        $contact = Contact::findOne($contact_id);
        if ($contact === null) {
            throw new NotFoundHttpException('The requested contact does not exist.');
        }
        $curOrder = $contact->order;
        $contact->order = -1;
        $contact->save();
        if ($direction == 'up') {
            $contactOther = Contact::findOne(['person_id' => $id, 'order' => $curOrder - 1]);
            $contactOther->order = $curOrder;
            $contactOther->save();
            $contact->order = $curOrder - 1;
            $contact->save();
        } else {
            $contactOther = Contact::findOne(['person_id' => $id, 'order' => $curOrder + 1]);
            $contactOther->order = $curOrder;
            $contactOther->save();
            $contact->order = $curOrder + 1;
            $contact->save();
        }
        $model = $this->findModel($id);
        return $this->renderAjax('_contact_view', ['model' => $model]);
    }

    public function actionDeleteContact($id)
    {
        $contact_id = $this->request->post('contact_id');
        $curContact = Contact::findOne($contact_id);
        if ($curContact === null) {
            throw new NotFoundHttpException('The requested contact does not exist.');
        }
        $curOrder = $curContact->order;
        $curContact->delete();
        $contacts = Contact::find()->where(['person_id' => $id])
            ->andWhere(['>', 'order', $curOrder])
            ->orderBy(['order' => SORT_ASC])->all();
        foreach ($contacts as $contact) {
            $contact->order = $contact->order - 1;
            $contact->save();
        }
        $model = $this->findModel($id);
        return $this->renderAjax('_contact_view', ['model' => $model]);
    }

    public function actionUpdateRelation($id)
    {
        $model = $this->findModel($id);
        return $this->render('update-relation', [
            'model' => $model,
        ]);
    }

    public function actionDeleteRelation($id, $rel_id)
    {
        $model = PersonRelation::findOne($rel_id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested person_relation does not exist.');
        }
        $model->delete();
        return $this->redirect(['update-relation', 'id' => $id]);
    }

    public function actionAddRelation($id, $mode)
    {
        $model = $this->findModel($id);
        $relation = new PersonRelation();
        if ($mode == 'A') {
            $relation->to_person_id = $id;
        } else {
            $relation->from_person_id = $id;
        }

        if ($this->request->isPost && $relation->load($this->request->post())) {
            if ($relation->validate()) {
                if ($relation->checkWithPerson($model, $mode)) {
                    if ($relation->save(false)) {
                        return $this->redirect(['update-relation', 'id' => $id]);
                    }
                }
            }
        }

        return $this->render('add-relation', [
            'model' => $model,
            'relation' => $relation,
            'mode' => $mode,
        ]);
    }
}

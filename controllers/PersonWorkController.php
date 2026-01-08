<?php

namespace app\controllers;

use app\models\Contact;
use app\models\Person;
use app\models\PersonWork;
use app\models\PersonWorkForm;
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
                        'import-tanada' => ['POST'],
                        'import-isg-forest' => ['POST'],
                        'add-link' => ['POST'],
                        'delete-link' => ['POST'],
                        'add-link-view' => ['POST'],
                        'delete-link-view' => ['POST'],
                        'delete-person' => ['POST'],
                        'reorder-contact' => ['POST'],
                        'delete-contact' => ['POST'],
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
            Yii::$app->session->setFlash('success', "$count 件の名簿ワークエントリを追加しました。");
        } else {
            Yii::$app->session->setFlash('warning', "0 件の名簿ワークエントリを追加しました。");
        }
        return $this->redirect(['index']);
    }

    public function actionImportForest()
    {
        $count = PersonWork::importFromForest();
        if ($count > 0) {
            Yii::$app->session->setFlash('success', "$count 件の名簿ワークエントリを追加しました。");
        } else {
            Yii::$app->session->setFlash('warning', "0 件の名簿ワークエントリを追加しました。");
        }
        return $this->redirect(['index']);
    }

    public function actionAddLink()
    {
        $id = $this->request->post('id');
        $person_id = $this->request->post('person_id');
        $model = $this->findModel($id);
        $model->person_id = $person_id;
        $model->save();

        return $this->asJson([
            'ok' => true,
            'id' => $model->id,
            'personRegisterHtml' => $this->renderPartial('_person_register', ['model' => $model]),
            'linkButtonsHtml' => $this->renderPartial('_link_buttons', ['model' => $model]),
            'linkPersonHtml' => $this->renderPartial('_link_person', ['model' => $model]),
        ]);
    }

    public function actionDeleteLink()
    {
        $id = $this->request->post('id');
        $model = $this->findModel($id);
        $model->person_id = null;
        $model->save();

        return $this->asJson([
            'ok' => true,
            'id' => $model->id,
            'personRegisterHtml' => $this->renderPartial('_person_register', ['model' => $model]),
            'linkButtonsHtml' => $this->renderPartial('_link_buttons', ['model' => $model]),
            'linkPersonHtml' => $this->renderPartial('_link_person', ['model' => $model]),
        ]);
    }

    public function actionAddLinkView($id)
    {
        $model = $this->findModel($id);
        $model->person_id = $this->request->post('person_id');
        $model->save();

        return $this->renderAjax('_person_view', ['model' => $model]);
    }

    public function actionDeleteLinkView($id)
    {
        $model = $this->findModel($id);
        $model->person_id = null;
        $model->save();
        return $this->renderAjax('_person_view', ['model' => $model]);
    }

    public function actionRegister($id, $route = null)
    {
        if ($route === null) {
            $route = ['index'];
        }
        $model = new PersonWorkForm();
        $model->readPersonWork($id);
        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->validate()) {
                if ($model->register()) {
                    return $this->redirect($route);
                }
            }
        }
        return $this->render('_person_work_form', ['model' => $model, 'route' => $route]);
    }

    public function actionUpdatePerson($id, $person_id)
    {
        $model = $this->findModel($id);
        $person = Person::findOne($model->person_id);
        if ($person === null) {
            throw new NotFoundHttpException('The requested person does not exist.');
        }
        if ($this->request->isPost && $person->load($this->request->post()) && $person->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update-person', [
            'model' => $model,
            'person' => $person,
        ]);
    }

    public function actionDeletePerson($id)
    {
        $person_id = $this->request->post('person_id');
        $person = Person::findOne($person_id);
        if ($person === null) {
            throw new NotFoundHttpException('The requested person does not exist.');
        }
        $person->delete();
        $model = $this->findModel($id);
        return $this->renderAjax('_person_view', ['model' => $model]);
    }

    public function actionCreateContact($id)
    {
        $model = $this->findModel($id);
        $person = Person::findOne($model->person_id);
        if ($person === null) {
            throw new NotFoundHttpException('The requested person does not exist.');
        }
        $contact = new Contact();
        $contact->person_id = $person->id;
        $contact->order = count($person->contacts) + 1;
        $contact->name1 = $person->name1;
        $contact->name2 = $person->name2;

        if ($this->request->isPost && $contact->load($this->request->post()) && $contact->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('create-contact', [
            'model' => $model,
            'person' => $person,
            'contact' => $contact,
        ]);
    }

    public function actionUpdateContact($id, $contact_id)
    {
        $model = $this->findModel($id);
        $person = Person::findOne($model->person_id);
        if ($person === null) {
            throw new NotFoundHttpException('The requested person does not exist.');
        }
        $contact = Contact::findOne($contact_id);
        if ($contact === null) {
            throw new NotFoundHttpException('The requested contact does not exist.');
        }

        if ($this->request->isPost && $contact->load($this->request->post()) && $contact->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update-contact', [
            'model' => $model,
            'person' => $person,
            'contact' => $contact,
        ]);
    }

    public function actionReorderContact($id)
    {
        $contact_id = $this->request->post('contact_id');
        $direction = $this->request->post('direction');
        $contact = Contact::findOne($contact_id);
        $curOrder = $contact->order;
        $contact->order = -1;
        $contact->save();
        if ($direction == 'up') {
            $contactOther = Contact::findOne(['person_id' => $contact->person_id, 'order' => $curOrder - 1]);
            $contactOther->order = $curOrder;
            $contactOther->save();
            $contact->order = $curOrder - 1;
            $contact->save();
        } else {
            $contactOther = Contact::findOne(['person_id' => $contact->person_id, 'order' => $curOrder + 1]);
            $contactOther->order = $curOrder;
            $contactOther->save();
            $contact->order = $curOrder + 1;
            $contact->save();
        }
        $model = $this->findModel($id);
        return $this->renderAjax('_person_view', ['model' => $model]);
    }

    public function actionDeleteContact($id)
    {
        $contact_id = $this->request->post('contact_id');
        $curContact = Contact::findOne($contact_id);
        $curOrder = $curContact->order;
        $person_id = $curContact->person_id;
        $curContact->delete();
        $contacts = Contact::find()->where(['person_id' => $person_id])
            ->andWhere(['>', 'order', $curOrder])
            ->orderBy(['order' => SORT_ASC])->all();
        foreach($contacts as $contact) {
            $contact->order = $contact->order - 1;
            $contact->save();
        }
        $model = $this->findModel($id);
        return $this->renderAjax('_person_view', ['model' => $model]);
    }
}

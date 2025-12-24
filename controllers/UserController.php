<?php

namespace app\controllers;

use app\models\User;
use app\models\UserRoleForm;
use app\models\UserSearch;
use Yii;
use yii\base\UserException;
use yii\bootstrap5\ActiveForm;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch(['show_system_user' => Yii::$app->user->can('admin')]);
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
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $user = $this->findModel($id);
        if (!Yii::$app->user->can('user.view', ['id' => $id] )) {
            throw new ForbiddenHttpException('このユーザの情報を閲覧する権限がありません。');
        }

        return $this->render('view', [
            'model' => $user,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return array|string|\string[][]|Response
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';

        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                if ($model->validate()) {
                    $model->setPassword($model->password);
                    $model->auth_key = Yii::$app->security->generateRandomString();
                    $model->save(false);
                    return $this->redirect(['index']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('user.edit', ['id' => $id] )) {
            throw new ForbiddenHttpException('このユーザの登録情報を変更する権限がありません。');
        }
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->validate()) {
                if (!empty($model->password)) {
                    $model->setPassword($model->password);
                }
                $model->save(false);
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionRole($id)
    {
        /* @var User $user */
        $user = User::findOne($id);
        if (!isset($user)) {
            throw new NotFoundHttpException('リクエストされたユーザは存在しません。');
        }
        if (!Yii::$app->user->can('user.edit_role')) {
            throw new ForbiddenHttpException('ユーザの権限を変更する権限がありません。');
        }

        $model = new UserRoleForm();
        $model->user_id = $user->id;

        $data = Yii::$app->request->post();
        if ($model->load($data)) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->validate()) {
                try {
                    $curRoles = Yii::$app->authManager->getRolesByUser($id);
                    $newRoles = $model->roles;
                    if (!is_array($newRoles)) {
                        $newRoles = [];
                    }
                    foreach($curRoles as $role) {
                        $key = array_search($role->name, $newRoles);
                        if ($key === false) {
                            Yii::$app->authManager->revoke($role, $id);
                        } else {
                            unset($newRoles[$key]);
                        }
                    }
                    foreach($newRoles as $roleName) {
                        $role = Yii::$app->authManager->getRole($roleName);
                        Yii::$app->authManager->assign($role, $id);
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
                return $this->redirect(['index']);
            }
        }
        $model->loadUserRoles();
        return $this->render('role', [
            'user' => $user,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('user.delete')) {
            throw new ForbiddenHttpException('このユーザを削除する権限がありません。');
        }

        /* @var User $user */
        $user = $this->findModel($id);

        try {
            $user->delete();
        }
        catch (IntegrityException $e) {
            throw new UserException('このユーザを参照しているデータが存在するため、削除することが出来ません。');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('リクエストされたユーザは存在しません。');
    }
}

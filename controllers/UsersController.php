<?php

namespace app\controllers;

use app\models\Access;
use app\models\form\UserUpdateForm;
use app\models\form\UserCreateForm;
use app\services\ServerService;
use app\services\UserService;
use Yii;
use app\models\search\UserSearch;
use yii\bootstrap4\ActiveForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

/**
 * Контроллер пользователей.
 */
class UsersController extends Controller
{
    /**
     * @var ServerService
     */
    private $serverService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageUsers'],
                    ]
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, UserService $userService, ServerService $serverService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userService = $userService;
        $this->serverService = $serverService;
    }

    /**
     * Возвращает список пользователей.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userService' => $this->userService
        ]);
    }

    /**
     * Создает пользователя.
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new UserCreateForm();

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
            return $this->redirect(['update', 'id' => $form->user->id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Обновляет пользователя.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        $form = new UserUpdateForm($this->userService->findById($id));

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }

    /**
     * Удаляет пользователя.
     * @param integer $id
     * @return mixed
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        try {
            $user = $this->userService->findById($id);
            $this->userService->delete($user, $id);
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        return $this->redirect('index');
    }

    /**
     * Удалялет привилегию пользователя.
     *
     * @param $userId
     * @param $serverId
     * @return int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeletePrivilege($userId, $serverId)
    {
        try {
            $user = $this->userService->findById($userId);
            $server = $this->serverService->findById($serverId);
            $this->userService->deleteAccess(
                $this->userService->findAccess($user, $server)
            );
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}

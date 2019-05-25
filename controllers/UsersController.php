<?php

namespace app\controllers;

use app\models\form\UserUpdateForm;
use app\models\form\UserForm;
use app\models\User;
use app\services\ServerService;
use app\services\UserService;
use Exception;
use Throwable;
use Yii;
use app\models\search\UserSearch;
use yii\bootstrap4\ActiveForm;
use yii\db\StaleObjectException;
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
        $form = new UserForm();

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Обновляет пользователя.
     * @param integer $id
     * @return mixed
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $form = new UserForm($this->findModel($id));

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
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
     * @throws Throwable
     * @throws UnprocessableEntityHttpException
     */
    public function actionDelete($id)
    {
        try {
            $user = $this->findModel($id);
            $this->userService->delete($user);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        return $this->redirect('index');
    }

    /**
     * Удалялет привилегию пользователя.
     *
     * @param $userId
     * @param $serverId
     * @return void
     * @throws Throwable
     * @throws UnprocessableEntityHttpException
     */
    public function actionDeletePrivilege($userId, $serverId)
    {
        try {
            $user = $this->findModel($userId);
            $server = $this->serverService->findById($serverId);
            $this->userService->deleteAccess(
                $this->userService->findAccess($user, $server)
            );
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Находит модель по идентификатору.
     *
     * @param integer $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        try {
            return $this->userService->getById($id);
        } catch (Exception $e) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
    }
}

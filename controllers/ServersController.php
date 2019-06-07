<?php

namespace app\controllers;

use app\models\search\ServerSearch;
use app\services\ServerService;
use Exception;
use Yii;
use app\models\Server;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Контроллер серверов.
 */
class ServersController extends Controller
{
    /**
     * @var ServerService
     */
    private $serverService;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['index', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageSettings'],
                    ]
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __construct($id, $module, ServerService $serverService, $config = [])
    {
        $this->serverService = $serverService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Список серверов.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $servers = $this->serverService->getServersInfo();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $servers
        ]);
    }

    /**
     * Отображение сервера.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $server = $this->serverService->getServerInfo($this->findModel($id));
        return $this->render('view', [
            'model' => $this->findModel($id),
            'server' => $server
        ]);
    }

    /**
     * Редактирование.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удаляет сервер.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Находит сервер по идентификатору.
     *
     * @param integer $id
     * @return Server
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        try {
            return $this->serverService->findById($id);
        } catch (Exception $e) {
            throw new NotFoundHttpException('Сервер не найден.');
        }
    }
}

<?php

namespace app\controllers;

use app\models\search\ServerSearch;
use app\services\ServerService;
use xPaw\SourceQuery\Exception\InvalidArgumentException;
use xPaw\SourceQuery\Exception\InvalidPacketException;
use xPaw\SourceQuery\Exception\TimeoutException;
use Yii;
use app\models\Server;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __construct($id, $module, ServerService $serverService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->serverService = $serverService;
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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Отображение сервера.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //$this->serverService->getOnline($this->findModel($id));
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Удаляет сервер.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
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
     * @return Server the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Server::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

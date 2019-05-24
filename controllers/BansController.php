<?php

namespace app\controllers;

use app\models\form\BanForm;
use Yii;
use app\models\Ban;
use app\models\search\BanSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Контроллер для работы с банами.
 */
class BansController extends Controller
{
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
            'access' => [
                'class' => AccessControl::class,
                'except' => ['index', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageBans'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Возвращает список банов.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Отображение бана.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Обновляет информацию о бане.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = new BanForm($this->findModel($id));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ban->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Разбанивает игрока.
     *
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUnban($id)
    {
        $model = $this->findModel($id);
        $model->ban_length = -1;
        $model->save();

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Находит модель по идентификатору.
     *
     * @param integer $id
     * @return Ban the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Ban::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'Запрошенная страница не найдена.'));
    }
}

<?php

namespace app\controllers;

use app\install\models\InstallForm;
use app\install\models\UserForm;
use app\install\services\InstallService;
use Yii;
use yii\web\Controller;

/**
 * Контроллер установки сайта.
 */
class InstallController extends Controller
{
    /**
     * @var bool
     */
    public $layout = false;

    /**
     * @var InstallService
     */
    private $installService;

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, InstallService $installService, $config = [])
    {
        $this->installService = $installService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Страница установки сайта.
     *
     * @return array|string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $this->installService->checkInstallation();

        $form = new InstallForm();
        if ($form->load(Yii::$app->request->post()) && $form->connect()) {
            $this->installService->install($form);
            return $this->redirect(['install/create-user']);
        }

        return $this->render('@app/install/views/index', ['action' => '_db' , 'model' => $form]);
    }

    /**
     * Создает пользователя.
     *
     * @return string
     */
    public function actionCreateUser()
    {
        $form = new UserForm();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->user->login($form->user, 3600 * 24 * 30);
            return $this->redirect(['site/index']);
        }

        return $this->render('@app/install/views/index', ['action' => '_user','model' => $form]);
    }
}

<?php

namespace app\controllers;

use app\install\models\DbForm;
use app\install\models\SettingsForm;
use app\install\models\UserForm;
use app\install\services\InstallService;
use Exception;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

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
     * @var bool флаг инициирования удаления самого себя.
     */
    private $needDelete = false;

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, InstallService $installService, $config = [])
    {
        $this->installService = $installService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Если флаг $needDelete выставлен в true, то удаляет самого себя.
     */
    public function __destruct()
    {
        if ($this->needDelete) {
            FileHelper::unlink(__FILE__);
        }
    }

    /**
     * Страница установки сайта.
     *
     * @return array|string
     * @throws Exception
     */
    public function actionIndex()
    {
        if ($this->installService->checkInstallation()) {
            throw new UnprocessableEntityHttpException('Сайт уже установлен.');
        }

        $form = new DbForm();
        if ($form->load(Yii::$app->request->post()) && $form->connect()) {
            $this->installService->install($form);
            return $this->redirect(['install/create-user']);
        }

        return $this->render('@app/install/views/index', ['action' => '_db', 'model' => $form]);
    }

    /**
     * Создает пользователя.
     *
     * @return string
     * @throws UnprocessableEntityHttpException
     */
    public function actionCreateUser()
    {
        if ($this->installService->checkUserExist()) {
            throw new UnprocessableEntityHttpException('Пользователь уже создан.');
        }

        $form = new UserForm();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->user->login($form->user, 3600 * 24 * 30);
            return $this->redirect(['install/settings']);
        }

        return $this->render('@app/install/views/index', ['action' => '_user', 'model' => $form]);
    }

    /**
     * Первоначальные настройки сайта.
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionSettings()
    {
        if ($this->installService->checkSettingsExist()) {
            throw new UnprocessableEntityHttpException('Настройки уже установлены.');
        }

        $form = new SettingsForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->installService->createSiteSettings($form);
            return $this->redirect(['install/finish']);
        }

        return $this->render('@app/install/views/index', ['action' => '_app', 'model' => $form]);
    }

    /**
     * Завершение установки.
     *
     * @throws Exception
     */
    public function actionFinish()
    {
        try {
            $this->installService->finishInstallation();
            $this->needDelete = true;
            return $this->redirect(['site/index']);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Произошла ошибка при удалении установочных файлов.' .
                ' Удалите папку install в корне проекта, а также, InstallController в папке controllers.');
        }
    }
}

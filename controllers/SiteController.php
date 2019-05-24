<?php

namespace app\controllers;

use app\models\form\PasswordResetRequestForm;
use app\models\form\ResendVerificationEmailForm;
use app\models\form\ResetPasswordForm;
use app\models\form\SignupForm;
use app\models\form\VerifyEmailForm;
use app\services\UserService;
use InvalidArgumentException;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\form\LoginForm;

class SiteController extends Controller
{
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
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['get', 'post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, UserService $userService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userService = $userService;
    }

    /**
     * Главная страница.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Страница входа.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Авторизация по уникальной ссылке.
     *
     * @param $code
     */
    public function actionAuth($code)
    {
        $user = $this->userService->findByCode($code);

        if ($user !== null) {
            Yii::$app->user->login($user, 3600 * 24 * 30);
        }

        $this->goHome();
    }

    /**
     * Выйти.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Регистрация.
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionSignup()
    {
        $this->layout = 'unauthorized';

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Спасибо за регистрацию.' .
                ' Пожалуйста, проверьте свой почтовый ящик для подтверждения по электронной почте.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Запрос на сброс пароля.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'unauthorized';

        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash(
                    'success',
                    'Проверьте электронную почту для получения дальнейших инструкций.'
                );

                return $this->goHome();
            }

            Yii::$app->session->setFlash(
                'error',
                'К сожалению, мы не можем сбросить пароль для предоставленного адреса электронной почты.'
            );
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Сброс пароля.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'unauthorized';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Подтверждение электронной почты.
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        $this->layout = 'unauthorized';

        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Ваш почтовый ящик был подтвержден!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'К сожалению, мы не можем подтвердить ваш аккаунт с помощью предоставленного токена.');
        return $this->goHome();
    }

    /**
     * Восстановление доступа.
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $this->layout = 'unauthorized';

        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для дальнейших инструкций.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash(
                'error',
                'К сожалению, мы не можем переслать подтверждающее письмо на указанный адрес электронной почты.'
            );
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /*private function createRules()
    {
        $auth = Yii::$app->authManager;

        $manageBans = $auth->createPermission('manageBans');
        $manageBans->description = 'Управление банами';
        $auth->add($manageBans);

        $manageContent = $auth->createPermission('manageContent');
        $manageContent->description = 'Управление контентом';
        $auth->add($manageContent);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Управление пользователями';
        $auth->add($manageUsers);

        $manageSettings = $auth->createPermission('manageSettings');
        $manageSettings->description = 'Управление настройками';
        $auth->add($manageSettings);

        $editor = $auth->createRole('editor');
        $auth->add($editor);
        $auth->addChild($editor, $manageContent);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageBans);

        $deputy = $auth->createRole('deputy');
        $auth->add($deputy);
        $auth->addChild($deputy, $editor);
        $auth->addChild($deputy, $admin);

        $root = $auth->createRole('root');
        $auth->add($root);
        $auth->addChild($root, $manageSettings);
        $auth->addChild($root, $deputy);

        // Назначение ролей пользователям. 1 и 2 это IDs возвращаемые IdentityInterface::getId()
        // обычно реализуемый в модели User.
        $auth->assign($root, 2);
        $auth->assign($editor, 1);
        $auth->assign($deputy, 3);
        var_dump(Yii::$app->authManager); die;
    }*/
}

<?php

namespace app\controllers;

use app\models\form\AccountForm;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class AccountController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Страница аккаунта.
     *
     * @return array|string
     */
    public function actionIndex()
    {
        $form = new AccountForm(\Yii::$app->user->identity);

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
        }

        return $this->render('index', ['model' => $form]);
    }
}

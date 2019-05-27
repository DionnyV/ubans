<?php

namespace app\install\models;

use app\models\User;
use Yii;

/**
 * Модель формы пользователя.
 */
class UserForm extends User
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string роль по умолчанию.
     */
    private $role = 'root';

    /**
     * {@inheritDoc}
     */
    public function __construct($config = [])
    {
        $this->user = new User();
        $this->user->scenario = User::SCENARIO_CREATE;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        if (isset($data['User'])) {
            $data['User']['nickname'] = $data['User']['username'];
            $data['User']['auth_key'] = Yii::$app->security->generateRandomString();
            $data['User']['status'] = User::STATUS_ACTIVE;
        }

        return $this->user->load($data, $formName);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->user->validate($attributeNames, $clearErrors);
    }

    /**
     * {@inheritDoc}
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $success = $this->user->save($runValidation, $attributeNames);

        if ($success) {
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($this->role);
            $auth->assign($authorRole, $this->user->id);
        }

        return $success;
    }
}

<?php

namespace app\models\form;

use app\models\RoleReference;
use app\models\User;
use app\services\UserService;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Модель формы создания пользователя.
 */
class UserForm extends User
{
    public $user;

    public $flag;

    public $options;

    public $role;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * {@inheritDoc}
     */
    public function __construct(?User $user = null, $config = [])
    {
        if ($user === null) {
            $this->user = new User(['scenario' => User::SCENARIO_CREATE]);
        } else {
            $this->user = $user;
        }
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->userService = Yii::$container->get(UserService::class);
        if ($this->user->isNewRecord) {
            $this->flag = User::ACCOUNT_FLAG_NICK;
            $this->role = RoleReference::DEFAULT_ROLE;
        } else {
            $this->flag = $this->userService->getFlag($this->user);
            $this->options = $this->userService->getOptions($this->user);

            $role = Yii::$app->authManager->getRolesByUser($this->user->id);
            if (!empty($role)) {
                $this->role = array_keys($role)[0];
            } else {
                $this->role = RoleReference::DEFAULT_ROLE;
            }
        }
        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        if (isset($data[$this->formName()])) {
            $form = $data[$this->formName()];
            if (!empty($form['flag'])) {
                $this->flag = $form['flag'];
                $this->user->flags = $this->flag;
            }
            if (!empty($form['options'])) {
                $this->options = $form['options'];
                $this->user->flags .= implode('', $this->options);
            }
            if (!empty($form['role'])) {
                $this->role = $form['role'];
            }
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
            $auth->revokeAll($this->user->id);
            $auth->assign($authorRole, $this->user->id);
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'role' => 'Права',
                'flag' => 'Доступ',
                'options' => 'Опции',
            ]
        );
    }
}

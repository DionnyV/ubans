<?php

namespace app\models\form;

use app\models\Access;
use app\models\RoleReference;
use app\models\Server;
use app\models\User;
use app\services\UserService;
use Yii;
use yii\helpers\ArrayHelper;

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
     * @var string
     */
    public $flag;

    /**
     * @var string
     */
    public $options;

    /**
     * @var string
     */
    public $role;

    /**
     * @var Access[]
     */
    public $privileges = [];

    /**
     * @var UserService
     */
    private $userService;

    /**
     * {@inheritDoc}
     */
    public function __construct(?User $user = null, $config = [])
    {
        $this->userService = Yii::$container->get(UserService::class);

        foreach (Server::find()->all() as $server) {
            $this->privileges[$server->id] = new Access([
                'server_id' => $server->id,
                'user_id' => '',
                'access_flags' => '',
                'expire' => time(),
            ]);
        }

        if ($user === null) {
            $this->user = new User(['scenario' => User::SCENARIO_CREATE]);
            $this->flag = User::ACCOUNT_FLAG_NICK;
            $this->role = RoleReference::DEFAULT_ROLE;
        } else {
            $this->user = $user;
            $this->flag = $this->userService->getFlag($this->user);
            $this->options = $this->userService->getOptions($this->user);
            $role = Yii::$app->authManager->getRolesByUser($this->user->id);
            if (!empty($role)) {
                $this->role = array_keys($role)[0];
            } else {
                $this->role = RoleReference::DEFAULT_ROLE;
            }

            foreach ($user->access as $access) {
                $this->privileges[$access->server_id] = $access;
            }
        }

        parent::__construct($config);
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
        $loadResult = true;
        if (isset($data['Access'])) {
            $loadResult = Access::loadMultiple($this->privileges, $data);
        }

        return $this->user->load($data, $formName) && $loadResult;
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
        $success = false;
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $success = $this->user->save($runValidation, $attributeNames);

            if ($success) {
                $auth = Yii::$app->authManager;
                $authorRole = $auth->getRole($this->role);
                $auth->revokeAll($this->user->id);
                $auth->assign($authorRole, $this->user->id);
            }

            foreach ($this->privileges as $server => $privilege) {
                if (empty($privilege->access_flags) || empty($privilege->expire)) {
                    continue;
                }
                $privilege->server_id = $server;
                $privilege->expire = strtotime($privilege->expire);
                $privilege->user_id = $this->user->id;
                if (!$privilege->save()) {
                    $success = false;
                    throw new \Exception('Произошла ошибка при сохранении привилегии.');
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
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

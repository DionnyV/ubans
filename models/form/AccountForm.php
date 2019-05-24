<?php

namespace app\models\form;

use app\models\Access;
use app\models\Ban;
use app\models\User;
use yii\web\IdentityInterface;

/**
 * Модель формы аккаунта.
 */
class AccountForm extends Ban
{
    /**
     * @var User;
     */
    public $user;

    /**
     * @var Access[]
     */
    public $privileges;

    /**
     * {@inheritDoc}
     */
    public function __construct(IdentityInterface $user, $config = [])
    {
        $this->user = $user;
        /** @var User $user */
        $this->privileges = $user->getAccess()->with(['server', 'server.privileges'])->all();
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        if (isset($data['User'])) {
            $params = ['email', 'username', 'flags', 'status'];
            foreach ($params as $param) {
                if (isset($data['User'][$param])) {
                    unset($data['User'][$param]);
                }
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
        return $this->user->save($runValidation, $attributeNames);
    }
}

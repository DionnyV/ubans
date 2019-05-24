<?php

namespace app\models\form;

use app\models\User;

/**
 * Модель формы редактирования пользователя.
 */
class UserUpdateForm extends User
{
    public $userForm;

    public $privilegesForm;

    /**
     * {@inheritDoc}
     */
    public function __construct(User $user, $config = [])
    {
        $this->userForm = new UserCreateForm($user);
        $this->privilegesForm = new UserPrivilegesForm($user);
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        return $this->userForm->load($data) && $this->privilegesForm->load($data);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->userForm->validate() && $this->privilegesForm->validate();
    }

    /**
     * {@inheritDoc}
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        return $this->userForm->save() && $this->privilegesForm->save();
    }
}

<?php

namespace app\install\models;

use yii\base\Model;

/**
 * Модель установки настроек сайта.
 */
class SettingsForm extends Model
{
    /**
     * @var string назваение сайта.
     */
    public $name;

    /**
     * @var string почта администратора.
     */
    public $adminEmail;

    /**
     * @var string почта сайта.
     */
    public $supportEmail;

    /**
     * @var string ключ от api сервиса оправки почты.
     */
    public $apiKey;

    /**
     * @var string секретная строка для защиты кук.
     */
    private $cookieValidationKey;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['name', 'adminEmail', 'supportEmail', 'apiKey'], 'required'],
            [['adminEmail', 'supportEmail'], 'email'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название сайта',
            'adminEmail' => 'Почта администратора',
            'supportEmail' => 'Почта сайта',
            'apiKey' => 'API ключ сервиса SendGrid',
        ];
    }
}

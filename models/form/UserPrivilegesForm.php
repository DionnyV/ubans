<?php

namespace app\models\form;

use app\models\Access;
use app\models\Server;
use app\models\User;
use Yii;
use yii\base\Model;

/**
 * Модель формы привилегий пользователя.
 */
class UserPrivilegesForm extends Model
{
    public $privileges;
    private $user;

    /**
     * {@inheritDoc}
     */
    public function __construct(?User $user, $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        foreach (Server::find()->all() as $server) {
            $this->privileges[$server->id] = new Access([
                'server_id' => $server->id,
                'user_id' => $this->user->id,
            ]);
        }

        foreach ($this->user->access as $privilege) {
            $this->privileges[$privilege->server_id] = $privilege;
        }
        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        if (isset($data['Access'])) {
            $sortedData = [];
            foreach ($data['Access'] as $index => $privilege) {
                if (isset($privilege['enable']) && $privilege['enable'] === 'on') {
                    $privilege['expire'] = strtotime($privilege['expire']);
                    $sortedData[$index] = $privilege;
                } else {
                    unset($this->privileges[$index]);
                }
            }
            $data['Access'] = $sortedData;
        }
        if (empty($this->privileges)) {
            return true;
        }
        return Access::loadMultiple($this->privileges, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return Access::validateMultiple($this->privileges);
    }

    /**
     * {@inheritDoc}
     */
    public function save()
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            foreach ($this->privileges as $privilege) {
                $privilege->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}

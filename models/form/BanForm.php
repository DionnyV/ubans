<?php

namespace app\models\form;

use app\models\Ban;
use yii\helpers\ArrayHelper;

/**
 * Модель формы редактирования бана.
 */
class BanForm extends Ban
{
    /**
     * @var Ban
     */
    public $ban;

    /**
     * @var string дата истечения бана.
     */
    public $until;

    /**
     * {@inheritDoc}
     */
    public function __construct(Ban $ban, $config = [])
    {
        $this->ban = $ban;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        switch ($this->ban->ban_length) {
            case 0:
                $until = Ban::FOREVER;
                break;
            case -1:
                $until = Ban::UNBANNED;
                break;
            default:
                $until = $this->ban->ban_created + $this->ban->ban_length * 60;
                if ($until < time()) {
                    $until = Ban::EXPIRED;
                }
        }
        $this->until = $until;

        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        if (isset($data['BanForm'])) {
            $until = strtotime($data['BanForm']['until']);

            if ($until) {
                $this->ban->ban_length = abs(
                    round(
                        (strtotime($data['BanForm']['until']) - $this->ban->ban_created) / 60
                    )
                );
            }
        }
        if (isset($data['Ban'])) {
            $params = ['player_nick', 'player_id', 'player_ip', 'ban_created', 'server_name', 'admin_nick', 'admin_id'];
            foreach ($params as $param) {
                if (isset($data['Ban'][$param])) {
                    unset($data['Ban'][$param]);
                }
            }
        }
        return $this->ban->load($data, $formName);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->ban->validate($attributeNames, $clearErrors);
    }

    /**
     * {@inheritDoc}
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        return $this->ban->save($runValidation, $attributeNames);
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'until' => 'Истекает',
            ]
        );
    }
}

<?php

namespace app\models\form;

use app\models\Ban;
use app\services\BanService;
use Yii;
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
     * @var BanService
     */
    private $banService;

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
        $this->banService = Yii::$container->get(BanService::class);
        $this->until = BanService::getExpireData($this->ban);
        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        if (isset($data['BanForm'])) {
            $until = $this->banService->calculateBanLength($this->ban, $data['BanForm']['until']);

            if ($until) {
                $this->ban->ban_length = $until;
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

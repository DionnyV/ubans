<?php

namespace app\models\query;

use app\models\Ban;
use yii\db\ActiveQuery;

/**
 * Модель запросов банов.
 *
 * @see Ban
 */
class BanQuery extends ActiveQuery
{
    /**
     * Возвращает дейтсвующие баны.
     * @return BanQuery
     */
    public function active()
    {
        //todo
        return $this->andWhere([]);
    }

    /**
     * {@inheritdoc}
     * @return Ban[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Ban|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

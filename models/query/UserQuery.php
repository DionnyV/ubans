<?php

namespace app\models\query;

/**
 * Модель запросов пользователей.
 *
 * @see User
 */
class UserQuery extends \yii\db\ActiveQuery
{
    public function notDeleted()
    {
        return $this->andWhere(['is_deleted' => false]);
    }
}

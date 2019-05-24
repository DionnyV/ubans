<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[Amxadmins]].
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

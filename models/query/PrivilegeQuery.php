<?php

namespace app\models\query;

use app\models\Privilege;

/**
 * This is the ActiveQuery class for [[Privilege]].
 *
 * @see Privilege
 */
class PrivilegeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Privilege[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Privilege|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

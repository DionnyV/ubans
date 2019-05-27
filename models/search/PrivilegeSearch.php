<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Privilege;

/**
 * Поисковая модель привилегий.
 */
class PrivilegeSearch extends Privilege
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'server_id'], 'integer'],
            [['name', 'access_flags'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Privilege::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'server_id' => $this->server_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'access_flags', $this->access_flags]);

        return $dataProvider;
    }
}

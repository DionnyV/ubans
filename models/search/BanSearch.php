<?php

namespace app\models\search;

use app\models\Ban;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Поисковая модель банов.
 */
class BanSearch extends Ban
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ban_created', 'ban_length', 'ban_kicks', 'expired'], 'integer'],
            [
                [
                    'player_ip',
                    'player_id',
                    'player_nick',
                    'admin_ip',
                    'admin_id',
                    'admin_nick',
                    'ban_type',
                    'ban_reason',
                    'server_ip',
                    'server_name'
                ], 'safe'],
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
        $query = Ban::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['ban_created' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'bid' => $this->id,
            'ban_created' => $this->ban_created,
            'ban_length' => $this->ban_length,
            'ban_kicks' => $this->ban_kicks,
            'expired' => $this->expired,
        ]);

        $query->andFilterWhere(['like', 'player_ip', $this->player_ip])
            ->andFilterWhere(['like', 'player_id', $this->player_id])
            ->andFilterWhere(['like', 'player_nick', $this->player_nick])
            ->andFilterWhere(['like', 'admin_ip', $this->admin_ip])
            ->andFilterWhere(['like', 'admin_id', $this->admin_id])
            ->andFilterWhere(['like', 'admin_nick', $this->admin_nick])
            ->andFilterWhere(['like', 'ban_type', $this->ban_type])
            ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'server_ip', $this->server_ip])
            ->andFilterWhere(['like', 'server_name', $this->server_name]);

        return $dataProvider;
    }
}

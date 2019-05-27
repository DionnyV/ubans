<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Server;

/**
 * Поисковая модель серверов.
 */
class ServerSearch extends Server
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'timestamp', 'motd_delay', 'amxban_menu', 'reasons', 'timezone_fixx'], 'integer'],
            [['hostname', 'address', 'gametype', 'rcon', 'amxban_version', 'amxban_motd'], 'safe'],
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
        $query = Server::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'motd_delay' => $this->motd_delay,
            'amxban_menu' => $this->amxban_menu,
            'reasons' => $this->reasons,
            'timezone_fixx' => $this->timezone_fixx,
        ]);

        $query->andFilterWhere(['like', 'hostname', $this->hostname])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'gametype', $this->gametype])
            ->andFilterWhere(['like', 'rcon', $this->rcon])
            ->andFilterWhere(['like', 'amxban_version', $this->amxban_version])
            ->andFilterWhere(['like', 'amxban_motd', $this->amxban_motd]);

        return $dataProvider;
    }
}

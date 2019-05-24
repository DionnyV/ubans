<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Server;

/**
 * ServerinfoSearch represents the model behind the search form of `app\models\Serverinfo`.
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
        // bypass scenarios() implementation in the parent class
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

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
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

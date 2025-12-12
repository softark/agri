<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tanada;

/**
 * TanadaSearch represents the model behind the search form of `app\models\Tanada`.
 */
class TanadaSearch extends Tanada
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['geom', 'p_no', 'owner', 'cultivator', 'usage'], 'safe'],
            [['area'], 'number'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Tanada::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'area' => $this->area,
        ]);

        $query->andFilterWhere(['ilike', 'geom', $this->geom])
            ->andFilterWhere(['ilike', 'p_no', $this->p_no])
            ->andFilterWhere(['ilike', 'owner', $this->owner])
            ->andFilterWhere(['ilike', 'cultivator', $this->cultivator])
            ->andFilterWhere(['ilike', 'usage', $this->usage]);

        return $dataProvider;
    }
}

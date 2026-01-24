<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PersonRelation;
use yii\helpers\ArrayHelper;

/**
 * PersonRelationSearch represents the model behind the search form of `app\models\PersonRelation`.
 */
class PersonRelationSearch extends PersonRelation
{
    use LoadParamsTrait;

    public $name;

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'name' => '引継元・引継先',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'from_person_id', 'to_person_id', 'created_by', 'updated_by'], 'integer'],
            [['note', 'name', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params, $pageSize = 20)
    {
        $query = PersonRelation::find()
            ->leftJoin('person pf', 'pf.id=person_relation.from_person_id')
            ->leftJoin('person pt', 'pt.id=person_relation.to_person_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['from_person_id' => SORT_ASC],
                'attributes' => [
                    'from_person_id' => [
                        'asc' => ['pf.yomi' => SORT_ASC, 'pt.yomi' => SORT_ASC],
                        'desc' => ['pf.yomi' => SORT_DESC, 'pt.yomi' => SORT_DESC],
                    ],
                    'to_person_id' => [
                        'asc' => ['pt.yomi' => SORT_ASC, 'pf.yomi' => SORT_ASC],
                        'desc' => ['pt.yomi' => SORT_DESC, 'pf.yomi' => SORT_DESC],
                    ],
                    'note' => [
                        'asc' => ['note' => SORT_ASC, 'pf.yomi' => SORT_ASC, 'pt.yomi' => SORT_ASC],
                        'desc' => ['note' => SORT_DESC, 'pf.yomi' => SORT_ASC, 'pt.yomi' => SORT_ASC],
                    ],
                ],
            ],
        ]);

        $this->loadAndRememberParams($this, $dataProvider, $params);
        // $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'from_person_id' => $this->from_person_id,
            'to_person_id' => $this->to_person_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'person_relation.note', $this->note]);
        $query->andFilterWhere(['or',
            ['ilike', 'pf.name', $this->name],
            ['ilike', 'pf.yomi', $this->name],
            ['ilike', 'pt.name', $this->name],
            ['ilike', 'pt.yomi', $this->name]
        ]);

        return $dataProvider;
    }
}

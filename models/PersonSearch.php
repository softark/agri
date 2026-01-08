<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * PersonSearch represents the model behind the search form of `app\models\Person`.
 */
class PersonSearch extends Person
{
    /**
     * @var string フォーム名
     */
    public $_form_name = 'ps';

    use LoadParamsTrait;

    public $search_name;

    public $search_address;

    public $search_phone;

    /**
     * @return string フォーム名
     * @throws \yii\base\InvalidConfigException
     */
    public function formName()
    {
        return $this->_form_name;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'search_name' => '名前・よみがな',
                'search_address' => '郵便番号・住所',
                'search_phone' => '電話',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search_name', 'search_address', 'search_phone', 'note', 'type', 'status'], 'safe'],
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
        $query = Person::find()->leftJoin('contact', 'person.id = contact.person_id')->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => [
                    'status',
                    'name' => [
                        'asc' => ['name' => SORT_ASC, 'type' => SORT_ASC],
                        'desc' => ['name' => SORT_DESC, 'type' => SORT_ASC],
                    ],
                    'yomi',
                    'note' => [
                        'asc' => ['note' => SORT_ASC, 'yomi' => SORT_ASC, 'type' => SORT_ASC],
                        'desc' => ['note' => SORT_DESC, 'yomi' => SORT_ASC, 'type' => SORT_ASC],
                    ],
                    'type' => [
                        'asc' => ['type' => SORT_ASC, 'yomi' => SORT_ASC, 'name' => SORT_ASC],
                        'desc' => ['type' => SORT_DESC, 'yomi' => SORT_DESC, 'name' => SORT_DESC],
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
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'person.name1', $this->name1])
            ->andFilterWhere(['ilike', 'person.name2', $this->name2])
            ->andFilterWhere(['ilike', 'yomi1', $this->yomi1])
            ->andFilterWhere(['ilike', 'yomi2', $this->yomi2])
            ->andFilterWhere(['ilike', 'person.note', $this->note]);

        if ($this->search_name != '') {
            $query->andWhere(['or',
                ['ilike', 'person.name', $this->search_name],
                ['ilike', 'yomi', $this->search_name],
            ]);
        }
        if ($this->search_address != '') {
            $query->andWhere(['or',
                ['ilike', 'contact.zip', $this->search_address],
                ['ilike', 'contact.address1', $this->search_address],
                ['ilike', 'contact.address2', $this->search_address],
            ]);
        }
        if ($this->search_phone != '') {
            $query->andWhere(['or',
                ['ilike', 'contact.phone1', $this->search_phone],
                ['ilike', 'contact.phone2', $this->search_phone],
            ]);
        }
        return $dataProvider;
    }
}

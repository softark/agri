<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Field;
use yii\helpers\ArrayHelper;

/**
 * FieldSearch represents the model behind the search form of `app\models\Field`.
 */
class FieldSearch extends Field
{
    public string $_form_name = 'fs';

    use LoadParamsTrait;

    public ?string $search_name = null;

    public $search_usage = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'aza_id', 'created_by', 'updated_by'], 'integer'],
            [['p_no', 'note', 'search_name', 'search_usage', 'created_at', 'updated_at'], 'safe'],
            [['c_area', 'f_area'], 'number'],
        ];
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'search_name' => '所有者・耕作者',
                'search_usage' => '農地利用状況',
            ]);
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
     * @param int|null $pageSize
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20)
    {
        $query = Field::find()
            ->leftJoin('aza a', 'a.id = field.aza_id')
            ->leftJoin('field_person fpo', 'fpo.field_id = field.id and fpo.role = 1 and fpo.valid_to IS null')
            ->leftJoin('person po', 'po.id = fpo.person_id')
            ->leftJoin('field_person fpc', 'fpc.field_id = field.id and fpc.role = 2 and fpc.valid_to IS null')
            ->leftJoin('person pc', 'pc.id = fpc.person_id')
            ->leftJoin('field_usage fu', 'fu.field_id = field.id and fu.valid_to IS null')
            ->leftJoin('usage u', 'u.id = fu.usage_id')
            ->with([
                'aza',
                'ownerFieldPerson',
                'ownerFieldPerson.person',
                'cultivatorFieldPerson',
                'cultivatorFieldPerson.person',
                'fieldUsage',
                'fieldUsage.usage',
            ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['p_no' => SORT_ASC],
                'attributes' => [
                    'aza_id' => [
                        'asc' => ['a.name' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['a.name' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'p_no' => [
                        'asc' => ['p_no_sort' => SORT_ASC, 'a.name' => SORT_ASC],
                        'desc' => ['p_no_sort' => SORT_DESC, 'a.name' => SORT_ASC],
                    ],
                    'owner' => [
                        'asc' => ['po.yomi' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['po.yomi' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'cultivator' => [
                        'asc' => ['pc.yomi' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['pc.yomi' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'usage' => [
                        'asc' => ['u.type' => SORT_ASC, 'u.order' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['u.type' => SORT_DESC, 'u.order' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'c_area',
                    'f_area',
                    'note' => [
                        'asc' => ['note' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['note' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                ]
            ]
        ]);

        $this->loadAndRememberParams($this, $dataProvider, $params);
        // $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'aza_id' => $this->aza_id,
            'c_area' => $this->c_area,
            'f_area' => $this->f_area,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'p_no', $this->p_no])
            ->andFilterWhere(['ilike', 'field.note', $this->note]);

        if ($this->search_name != '') {
            $query->andWhere(['or',
                ['ilike', 'po.name', $this->search_name],
                ['ilike', 'pc.name', $this->search_name],
                ['ilike', 'po.yomi', $this->search_name],
                ['ilike', 'pc.yomi', $this->search_name]
            ]);
        }

        if ($this->search_usage != '') {
            if (sscanf($this->search_usage, 'T%d', $val) !== 0) {
                $query->andWhere(['u.type' => $val]);
            } else {
                $query->andWhere(['u.id' => $this->search_usage]);
            }
        }

        return $dataProvider;
    }

    public
    static function getFAreaTotal($dataProvider)
    {
        $query = clone($dataProvider->query);
        return $query->limit(-1)->offset(-1)->orderBy([])->sum('f_area');
    }

    public
    static function getCAreaTotal($dataProvider)
    {
        $query = clone($dataProvider->query);
        return $query->limit(-1)->offset(-1)->orderBy([])->sum('c_area');
    }
}

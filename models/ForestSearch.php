<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Forest;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * ForestSearch represents the model behind the search form of `app\models\Forest`.
 */
class ForestSearch extends Forest
{
    use LoadParamsTrait;

    public ?string $search_name = null;
    public ?string $owner_name = null;
    public ?string $manager_name = null;

    public $search_person_id = null;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'aza_id', 'type_id', 'search_person_id', 'created_by', 'updated_by'], 'integer'],
            [['p_no', 'note', 'search_name', 'owner_name', 'manager_name', 'created_at', 'updated_at'], 'safe'],
            [['area'], 'number'],
        ];
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'search_name' => '関係者(所/管)',
                'owner_name' => '所有者',
                'manager_name' => '所有者',
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
    public function search($params, $pageSize = 20, $sessionKey = null)
    {
        $params = $this->rememberGridParams($params, 'fo-page', 'fo-sort', $sessionKey);

        $query = Forest::find()
            ->leftJoin('aza a', 'a.id = forest.aza_id')
            ->leftJoin('frtype ft', 'ft.id = forest.type_id')
            ->leftJoin('forest_person fpo', 'fpo.forest_id = forest.id and fpo.role = 1 and fpo.valid_to IS null')
            ->leftJoin('person po', 'po.id = fpo.person_id')
            ->leftJoin('forest_person fpm', 'fpm.forest_id = forest.id and fpm.role = 2 and fpm.valid_to IS null')
            ->leftJoin('person pm', 'pm.id = fpm.person_id')
            ->with([
                'aza',
                'type',
                'ownerForestPerson',
                'ownerForestPerson.person',
                'managerForestPerson',
                'managerForestPerson.person',
            ])
            ->select([
                'forest.*',
                'public.ST_XMin(bbox_3857) AS xmin',
                'public.ST_YMin(bbox_3857) AS ymin',
                'public.ST_XMax(bbox_3857) AS xmax',
                'public.ST_YMax(bbox_3857) AS ymax',
                'public.ST_X(center_3857)  AS cx',
                'public.ST_Y(center_3857)  AS cy'
            ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'pageParam' => 'fo-page',
                'params'    => $params,
            ],
            'sort' => [
                'sortParam' => 'fo-sort',
                'params'    => $params,
                'defaultOrder' => ['p_no' => SORT_ASC],
                'attributes' => [
                    'aza_id' => [
                        'asc' => ['a.name' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['a.name' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'p_no' => [
                        'asc' => ['p_no_sort' => SORT_ASC],
                        'desc' => ['p_no_sort' => SORT_DESC],
                    ],
                    'type_id' => [
                        'asc' => ['ft.order' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['ft.order' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'owner' => [
                        'asc' => ['po.yomi' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['po.yomi' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'manager' => [
                        'asc' => ['pm.yomi' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['pm.yomi' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'area',
                    'note' => [
                        'asc' => ['note' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['note' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                ]
            ]
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
            'aza_id' => $this->aza_id,
            'type_id' => $this->type_id,
            'area' => $this->area,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['ilike', 'p_no', $this->p_no])
            ->andFilterWhere(['ilike', 'forest.note', $this->note]);

        if ($this->owner_name != '') {
            $query->andWhere(['or',
                ['ilike', 'po.name', $this->owner_name],
                ['ilike', 'po.yomi', $this->owner_name],
            ]);
        }
        if ($this->manager_name != '') {
            $query->andWhere(['or',
                ['ilike', 'pm.name', $this->manager_name],
                ['ilike', 'pm.yomi', $this->manager_name],
            ]);
        }
        if ($this->search_name != '') {
            $query->andWhere(['or',
                ['ilike', 'po.name', $this->search_name],
                ['ilike', 'pm.name', $this->search_name],
                ['ilike', 'po.yomi', $this->search_name],
                ['ilike', 'pm.yomi', $this->search_name]
            ]);
        }

        if ($this->search_person_id) {
            $query->andWhere(['or',
                ['fpo.person_id' => $this->search_person_id],
                ['fpm.person_id' => $this->search_person_id],
            ]);
        }

        return $dataProvider;
    }

    public static function getAreaTotal($dataProvider)
    {
        $query = clone($dataProvider->query);
        return $query->limit(-1)->offset(-1)->orderBy([])->sum('area');
    }

    public static function getModelIds($dataProvider)
    {
        $query = clone($dataProvider->query);
        $rows = $query->limit(-1)->offset(-1)->orderBy([])->select(['forest.id'])->all();
        return array_column($rows, 'id');
    }

    public static function getBboxTotal(array $ids): ?array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) return null;

        // PostgreSQL: 配列パラメータは '{1,2,3}' 形式で渡すのが楽
        $pgArray = '{' . implode(',', $ids) . '}';

        $sub = (new Query())
            ->from('agri.forest')
            ->select([
                'e' => new Expression('public.ST_Extent(bbox_3857)')
            ])
            ->where(new Expression('id = ANY(:ids::int[])', [':ids' => $pgArray]));

        $row = (new Query())
            ->from(['s' => $sub])
            ->select([
                'xmin' => new Expression('public.ST_XMin(s.e)'),
                'ymin' => new Expression('public.ST_YMin(s.e)'),
                'xmax' => new Expression('public.ST_XMax(s.e)'),
                'ymax' => new Expression('public.ST_YMax(s.e)'),
            ])
            ->one();

        if (!$row || $row['xmin'] === null) return null;
        return $row;
    }
}

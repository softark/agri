<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Field;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * FieldSearch represents the model behind the search form of `app\models\Field`.
 */
class FieldSearch extends Field
{
    use LoadParamsTrait;

    public ?string $search_name = null;

    public $search_usage = null;

    public $search_person_id = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'aza_id', 'search_person_id', 'created_by', 'updated_by'], 'integer'],
            [['p_no', 'note', 'search_name', 'search_usage', 'created_at', 'updated_at'], 'safe'],
            [['c_area', 'f_area'], 'number'],
        ];
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'search_name' => '関係者',
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
    public function search($params, $pageSize = 20, $sessionKey = null)
    {
        $params = $this->rememberGridParams($params, 'fl-page', 'fl-sort', $sessionKey);

        $query = Field::find()
            ->leftJoin('aza a', 'a.id = field.aza_id')
            ->leftJoin('field_person fpo', 'fpo.field_id = field.id and fpo.role = 1 and fpo.valid_to IS null')
            ->leftJoin('person po', 'po.id = fpo.person_id')
            ->leftJoin('field_person fpc', 'fpc.field_id = field.id and fpc.role = 2 and fpc.valid_to IS null')
            ->leftJoin('person pc', 'pc.id = fpc.person_id')
            ->leftJoin('field_person fpch', 'fpch.field_id = field.id and fpch.role = 3 and fpch.valid_to IS null')
            ->leftJoin('person pch', 'pch.id = fpch.person_id')
            ->leftJoin('field_person fpsa', 'fpsa.field_id = field.id and fpsa.role = 4 and fpsa.valid_to IS null')
            ->leftJoin('person psa', 'psa.id = fpsa.person_id')
            ->leftJoin('field_usage fu', 'fu.field_id = field.id and fu.valid_to IS null')
            ->leftJoin('usage u', 'u.id = fu.usage_id')
            ->with([
                'aza',
                'ownerFieldPerson',
                'ownerFieldPerson.person',
                'cultivatorFieldPerson',
                'cultivatorFieldPerson.person',
                'chusankanFieldPerson',
                'chusankanFieldPerson.person',
                'saimokushoFieldPerson',
                'saimokushoFieldPerson.person',
                'fieldUsage',
                'fieldUsage.usage',
            ])
            ->select([
                'field.*',
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
                'pageParam' => 'fl-page',
                'params' => $params,
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'sortParam' => 'fl-sort',
                'params' => $params,
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
                    'chusankan' => [
                        'asc' => ['pch.yomi' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['pch.yomi' => SORT_DESC, 'p_no_sort' => SORT_ASC],
                    ],
                    'saimokusho' => [
                        'asc' => ['psa.yomi' => SORT_ASC, 'p_no_sort' => SORT_ASC],
                        'desc' => ['psa.yomi' => SORT_DESC, 'p_no_sort' => SORT_ASC],
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
                ['ilike', 'pch.name', $this->search_name],
                ['ilike', 'psa.name', $this->search_name],
                ['ilike', 'po.yomi', $this->search_name],
                ['ilike', 'pc.yomi', $this->search_name],
                ['ilike', 'pch.yomi', $this->search_name],
                ['ilike', 'psa.yomi', $this->search_name]
            ]);
        }

        if ($this->search_person_id) {
            $query->andWhere(['or',
                ['fpo.person_id' => $this->search_person_id],
                ['fpc.person_id' => $this->search_person_id],
                ['fpch.person_id' => $this->search_person_id],
                ['fpsa.person_id' => $this->search_person_id],
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

    public
    static function getModelIds($dataProvider)
    {
        $query = clone($dataProvider->query);
        $rows = $query->limit(-1)->offset(-1)->orderBy([])->select(['field.id'])->all();
        return array_column($rows, 'id');
    }

    public static function getBboxTotal(array $ids): ?array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) return null;

        // PostgreSQL: 配列パラメータは '{1,2,3}' 形式で渡すのが楽
        $pgArray = '{' . implode(',', $ids) . '}';

        $sub = (new Query())
            ->from('agri.field')
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

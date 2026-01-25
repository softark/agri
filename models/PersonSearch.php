<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Expression;

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

    const HAS_UNDEF = 0;
    const HAS_NONE = 1;
    const HAS_FIELD = 2;
    const HAS_FOREST = 3;
    const HAS_FIELD_OR_FOREST = 4;
    const HAS_FIELD_AND_FOREST = 5;

    public static function relationLabels()
    {
        return [
            self::HAS_UNDEF => '(不問)',
            self::HAS_NONE => 'なし',
            self::HAS_FIELD => '農地',
            self::HAS_FOREST => '山林',
            self::HAS_FIELD_OR_FOREST => '農地または山林',
            self::HAS_FIELD_AND_FOREST => '農地と山林',
        ];
    }

    public $relation_type = self::HAS_UNDEF;

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
                'relation_type' => '農地・山林との関係',
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
            [['relation_type', 'search_name', 'search_address', 'search_phone', 'note', 'type', 'status'], 'safe'],
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
    public function search($params, $pageSize = 20, $sessionKey = null)
    {
        $params = $this->rememberGridParams($params, 'p-page', 'p-sort', $sessionKey);

        $fp = FieldPerson::tableName();   // 'field_person'
        $rp = ForestPerson::tableName();  // 'forest_person'
        $c = Contact::tableName();       // 'contact'

        // 農地 owner/cultivator の (person_id, field_id) を UNION
        $f_owner = (new Query())
            ->select(['person_id', 'field_id'])
            ->from($fp)
            ->where([$fp . '.role' => FieldPerson::ROLE_OWNER]);
        $f_cult = (new Query())
            ->select(['person_id', 'field_id'])
            ->from($fp)
            ->where([$fp . '.role' => FieldPerson::ROLE_CULTIVATOR]);
        $f_u = (new Query())->from(['f_u' => $f_owner->union($f_cult, false)]); // false = UNION
        // person_id ごとに数える
        $f_agg = (new Query())
            ->select([
                'person_id',
                'num_fields' => new Expression('COUNT(*)'),
            ])
            ->from(['f_t' => $f_u])
            ->groupBy('person_id');

        // 山林 owner/manager の (person_id, forest_id) を UNION
        $r_owner = (new Query())
            ->select(['person_id', 'forest_id'])
            ->from($rp)
            ->where([$rp . '.role' => ForestPerson::ROLE_OWNER]);
        $r_manager = (new Query())
            ->select(['person_id', 'forest_id'])
            ->from($rp)
            ->where([$rp . '.role' => ForestPerson::ROLE_MANAGER]);
        $r_u = (new Query())->from(['r_u' => $r_owner->union($r_manager, false)]); // false = UNION

        // person_id ごとに数える
        $r_agg = (new Query())
            ->select([
                'person_id',
                'num_forests' => new Expression('COUNT(*)'),
            ])
            ->from(['r_t' => $r_u])
            ->groupBy('person_id');

        $query = Person::find()
            ->alias('p')
            ->select([
                'p.*',
                'num_fields' => new Expression('COALESCE(ff.num_fields, 0)'),
                'num_forests' => new Expression('COALESCE(rf.num_forests, 0)'),
            ])
            ->leftJoin(['ff' => $f_agg], 'ff.person_id = p.id')
            ->leftJoin(['rf' => $r_agg], 'rf.person_id = p.id')
            ->leftJoin([
                new Expression(
                    'LATERAL (
                        SELECT *
                        FROM ' . $c . ' c
                        WHERE c.person_id = p.id
                        ORDER BY c."order" ASC, c.id ASC
                        LIMIT 1
                    ) c ON true'
                )
            ])
            ->with(['contacts']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'pageParam' => 'p-page',
                'params'    => $params,
            ],
            'sort' => [
                'sortParam' => 'p-sort',
                'params'    => $params,
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => [
                    'status',
                    'num_fields' => [
                        'asc' => ['num_fields' => SORT_ASC, 'yomi' => SORT_ASC, 'type' => SORT_ASC],
                        'desc' => ['num_fields' => SORT_DESC, 'yomi' => SORT_ASC, 'type' => SORT_ASC],
                    ],
                    'num_forests' => [
                        'asc' => ['num_forests' => SORT_ASC, 'yomi' => SORT_ASC, 'type' => SORT_ASC],
                        'desc' => ['num_forests' => SORT_DESC, 'yomi' => SORT_ASC, 'type' => SORT_ASC],
                    ],
                    'name' => [
                        'asc' => ['yomi' => SORT_ASC, 'type' => SORT_ASC],
                        'desc' => ['yomi' => SORT_DESC, 'type' => SORT_ASC],
                    ],
                    'c_name' => [
                        'asc' => ['c.name' => SORT_ASC, 'yomi' => SORT_ASC],
                        'desc' => ['c.name' => SORT_DESC, 'yomi' => SORT_ASC],
                    ],
                    'c_address' => [
                        'asc' => ['c.address1' => SORT_ASC, 'yomi' => SORT_ASC],
                        'desc' => ['c.address1' => SORT_DESC, 'yomi' => SORT_ASC],
                    ],
                    'c_phone' => [
                        'asc' => ['c.phone1' => SORT_ASC, 'yomi' => SORT_ASC],
                        'desc' => ['c.phone2' => SORT_DESC, 'yomi' => SORT_ASC],
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

        $this->load($params);

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

        $query->andFilterWhere(['ilike', 'p.name1', $this->name1])
            ->andFilterWhere(['ilike', 'p.name2', $this->name2])
            ->andFilterWhere(['ilike', 'yomi1', $this->yomi1])
            ->andFilterWhere(['ilike', 'yomi2', $this->yomi2])
            ->andFilterWhere(['ilike', 'p.note', $this->note]);

        if ($this->search_name != '') {
            $query->andWhere(['or',
                ['ilike', 'p.name', $this->search_name],
                ['ilike', 'yomi', $this->search_name],
                ['ilike', 'c.name', $this->search_name],
            ]);
        }
        if ($this->search_address != '') {
            $query->andWhere(['or',
                ['ilike', 'c.zip', $this->search_address],
                ['ilike', 'c.address1', $this->search_address],
                ['ilike', 'c.address2', $this->search_address],
            ]);
        }
        if ($this->search_phone != '') {
            $query->andWhere(['or',
                ['ilike', 'c.phone1', $this->search_phone],
                ['ilike', 'c.phone2', $this->search_phone],
            ]);
        }

        switch ($this->relation_type) {
            case self::HAS_UNDEF:
                break;
            case self::HAS_NONE:
                $query->andWhere(['and', 'ff.person_id is null', 'rf.person_id is null']);
                break;
            case self::HAS_FIELD:
                $query->andWhere('ff.person_id is not null');
                break;
            case self::HAS_FOREST:
                $query->andWhere('rf.person_id is not null');
                break;
            case self::HAS_FIELD_OR_FOREST:
                $query->andWhere(['or', 'ff.person_id is not null', 'rf.person_id is not null']);
                break;
            case self::HAS_FIELD_AND_FOREST:
                $query->andWhere(['and', 'ff.person_id is not null', 'rf.person_id is not null']);
                break;
        }

        return $dataProvider;
    }
}

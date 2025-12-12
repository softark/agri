<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * @var string 検索する名前
     */
    public $search_name;

    /**
     * @var bool システムユーザを含むかどうか
     */
    public $show_system_user = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['username', 'dispname', 'search_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ユーザ名',
            'dispname' => '表示名',
            'roleText' => '権限',
            'search_name' => 'ユーザ名・表示名',
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function search($params = [], $pageSize = 10)
    {
        $query = UserSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'username',
                    'dispname',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->search_name)) {
            $query->andWhere([
                'or',
                ['like', 'username', $this->search_name],
                ['like', 'dispname', $this->search_name],
            ]);
        }

        if (!$this->show_system_user) {
            $query->andWhere('id >= :id', ['id' => self::USER_NORMAL_START]);
        }

        return $dataProvider;
    }
}

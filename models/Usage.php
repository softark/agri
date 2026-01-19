<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "usage".
 *
 * @property int $id
 * @property int $type
 * @property int $order
 * @property string $name
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property FieldUsage[] $fieldUsages
 * @property User $updatedBy
 */
class Usage extends \yii\db\ActiveRecord
{

    public const TYPE_UNDEF = 0;
    public const TYPE_GRAIN = 1;
    public const TYPE_VEGETABLE = 2;
    public const TYPE_TREE = 3;
    public const TYPE_LANDSCAPE = 4;
    public const TYPE_CONSERVATION = 5;

    public static function getTypes()
    {
        return [
            self::TYPE_UNDEF => '----',
            self::TYPE_GRAIN => '穀物',
            self::TYPE_VEGETABLE => '野菜',
            self::TYPE_TREE => '樹木',
            self::TYPE_LANDSCAPE => '景観作物',
            self::TYPE_CONSERVATION => '農地保全',
        ];
    }

    public function getTypeText()
    {
        return self::getTypes()[$this->type];
    }

    private static $_type_and_usage_list = [];

    static public function getTypeAndUsageList()
    {
        if (empty(self::$_type_and_usage_list)) {
            $rows = Usage::find()
                ->select(['id', 'name', 'type', 'order'])
                ->orderBy(['type' => SORT_ASC, 'order' => SORT_ASC])
                ->asArray()->all();
            foreach (self::getTypes() as $type => $name) {
                self::$_type_and_usage_list['T' . $type] = $name;
                foreach ($rows as $row) {
                    if ($row['type'] == $type && $row['name'] != '----') {
                        self::$_type_and_usage_list[$row['id']] = $name . ' - ' . $row['name'];
                    }
                }
            }
        }
        return self::$_type_and_usage_list;
    }

    private static $_usage_list = [];

    static public function getUsageList()
    {
        if (empty(self::$_usage_list)) {
            $rows = Usage::find()
                ->select(['id', 'name', 'type', 'order'])
                ->orderBy(['type' => SORT_ASC, 'order' => SORT_ASC])
                ->asArray()->all();
            self::$_usage_list = ArrayHelper::map($rows, 'id', 'name',
                function (array $row) { return self::getTypes()[$row['type']]; });
        }
        return self::$_usage_list;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order'], 'default', 'value' => 100],
            [['type'], 'default', 'value' => 0],
            [['type'], 'in', 'range' => array_keys(self::getTypes())],
            [['updated_by'], 'default', 'value' => 1],
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [['type', 'order', 'created_by', 'updated_by'], 'integer'],
            [['name', 'type', 'order'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 30],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '種別',
            'order' => '表示順',
            'name' => '名前',
            'created_at' => '登録日時',
            'created_by' => '登録者',
            'updated_at' => '更新日時',
            'updated_by' => '更新者',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[FieldUsages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFieldUsages()
    {
        return $this->hasMany(FieldUsage::class, ['usage_id' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public
    function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $user_id = (Yii::$app->user->isGuest) ? 1 : Yii::$app->user->id;
            if ($insert) {
                $this->created_by = $user_id;
            }
            $this->updated_by = $user_id;
            $dt = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
            $this->updated_at = $dt->format("Y-m-d H:i:s T");
            return true;
        } else {
            return false;
        }
    }

}

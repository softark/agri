<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "person_work".
 *
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property int|null $person_id
 *
 * @property Person $person
 */
class PersonWork extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person_work';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'person_id'], 'default', 'value' => null],
            [['name'], 'required'],
            [['person_id'], 'default', 'value' => null],
            [['person_id'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['address'], 'string', 'max' => 100],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['person_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '氏名',
            'address' => '住所',
            'person_id' => '住所カード',
        ];
    }

    /**
     * Gets query for [[Person]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'person_id']);
    }

    /**
     * @return int
     * @throws \yii\db\Exception
     */
    public static function importFromTanada() : int
    {
        $count = 0;
        $tanadas = Tanada::find()->select(['owner'])->distinct()->all();
        foreach ($tanadas as $tanada) {
            if ($tanada->owner != '') {
                if (self::find()->where(['name' => $tanada->owner])->count() == 0) {
                    $pw = new PersonWork();
                    $pw->name = $tanada->owner;
                    $pw->save();
                    $count++;
                }
            }
        }
        $tanadas = Tanada::find()->select(['cultivator'])->distinct()->all();
        foreach ($tanadas as $tanada) {
            if (self::find()->where(['name' => $tanada->cultivator])->count() == 0) {
                if ($tanada->cultivator != '') {
                    $pw = new PersonWork();
                    $pw->name = $tanada->cultivator;
                    $pw->save();
                    $count++;
                }
            }
        }
        return $count;
    }

    public static function importFromForest() : int
    {
        $count = 0;
        $forests = Forest::find()->select(['owner', 'o_addr'])->distinct()->all();
        foreach ($forests as $forest) {
            if (self::find()->where(['name' => $forest->owner])
                    ->andWhere(['address' => $forest->o_addr])->count() == 0) {
                if ($forest->owner != '') {
                    $pw = new PersonWork();
                    $pw->name = $forest->owner;
                    $pw->address = $forest->o_addr;
                    $pw->save();
                    $count++;
                }
            }
        }
        $forests = Forest::find()->select(['manager', 'm_addr'])->distinct()->all();
        foreach ($forests as $forest) {
            if (self::find()->where(['name' => $forest->manager])
                    ->andWhere(['address' => $forest->m_addr])->count() == 0) {
                if ($forest->manager != '') {
                    $pw = new PersonWork();
                    $pw->name = $forest->manager;
                    $pw->address = $forest->m_addr;
                    $pw->save();
                    $count++;
                }
            }
        }
        return $count;
    }

}

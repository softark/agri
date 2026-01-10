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
 * @property int $src
 *
 * @property Person $person
 */
class PersonWork extends \yii\db\ActiveRecord
{

    public const SRC_NONE = 0;
    public const SRC_TANADA_OWNER = 1;
    public const SRC_TANADA_CULTIVATOR = 2;
    public const SRC_FOREST_OWNER = 3;
    public const SRC_FOREST_MANAGER = 4;

    public static function getSrcTypes()
    {
        return [
            self::SRC_NONE => '無し',
            self::SRC_TANADA_OWNER => '農-所有者',
            self::SRC_TANADA_CULTIVATOR => '農-耕作者',
            self::SRC_FOREST_OWNER => '山-所有者',
            self::SRC_FOREST_MANAGER => '山-管理者',
        ];
    }

    public function getSrcText()
    {
        return self::getSrcTypes()[$this->src];
    }

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
            [['person_id', 'src'], 'integer'],
            ['src', 'default', 'value' => self::SRC_NONE],
            ['src', 'in', 'range' => array_keys(self::getSrcTypes())],
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
            'name' => '名称',
            'address' => '住所',
            'src' => 'ソース',
            'person_id' => '名簿',
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
        $tanadas = IsgTanada::find()->select(['owner'])->distinct()->all();
        foreach ($tanadas as $tanada) {
            if ($tanada->owner != '') {
                if (self::find()->where(['name' => $tanada->owner, 'src' => PersonWork::SRC_TANADA_OWNER])->count() == 0) {
                    $pw = new PersonWork();
                    $pw->name = $tanada->owner;
                    $pw->src = PersonWork::SRC_TANADA_OWNER;
                    $pw->save();
                    $count++;
                }
            }
        }
        $tanadas = IsgTanada::find()->select(['cultivator'])->distinct()->all();
        foreach ($tanadas as $tanada) {
            if (self::find()->where(['name' => $tanada->cultivator, 'src' => PersonWork::SRC_TANADA_CULTIVATOR])->count() == 0) {
                if ($tanada->cultivator != '') {
                    $pw = new PersonWork();
                    $pw->name = $tanada->cultivator;
                    $pw->src = PersonWork::SRC_TANADA_CULTIVATOR;
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
        $forests = IsgForest::find()->select(['owner', 'o_addr'])->distinct()->all();
        foreach ($forests as $forest) {
            if (self::find()->where(['name' => $forest->owner, 'address' => $forest->o_addr, 'src' => PersonWork::SRC_FOREST_OWNER])->count() == 0) {
                if ($forest->owner != '') {
                    $pw = new PersonWork();
                    $pw->name = $forest->owner;
                    $pw->address = $forest->o_addr;
                    $pw->src = PersonWork::SRC_FOREST_OWNER;
                    $pw->save();
                    $count++;
                }
            }
        }
        $forests = IsgForest::find()->select(['manager', 'm_addr'])->distinct()->all();
        foreach ($forests as $forest) {
            if (self::find()->where(['name' => $forest->manager, 'address' => $forest->m_addr, 'src' => PersonWork::SRC_FOREST_MANAGER])->count() == 0) {
                if ($forest->manager != '') {
                    $pw = new PersonWork();
                    $pw->name = $forest->manager;
                    $pw->address = $forest->m_addr;
                    $pw->src = PersonWork::SRC_FOREST_MANAGER;
                    $pw->save();
                    $count++;
                }
            }
        }
        return $count;
    }

    public static function optimizeContactNames() : int
    {
        $count = 0;
        $contacts = Contact::find()->all();
        foreach ($contacts as $contact) {
            if ($contact->role == '' && $contact->name == $contact->person->name) {
                $contact->name1 = '';
                $contact->name2 = '';
                $contact->save();
                $count++;
            }
        }
        return $count;
    }

}

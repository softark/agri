<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 *
 */
class PersonForm extends Model
{
    public Person|null $person = null;
    public Contact|null $contact = null;

    public string $name1 = '';
    public string $name2 = '';
    public string $yomi1 = '';
    public string $yomi2 = '';
    public int $type = Person::TYPE_INDIVIDUAL;
    public string $person_note = '';
    public bool $has_contact = true;
    public string $role = '';
    public string $contact_name1 = '';
    public string $contact_name2 = '';
    public string $zip = '';
    public string $address1 = '';
    public string $address2 = '';
    public string $phone1 = '';
    public string $phone2 = '';
    public string $mail = '';
    public string $contact_note = '';

    private $_dispname = null;

    public function getDispName()
    {
        if ($this->_dispname === null) {
            $this->_dispname = trim($this->name1 . ' ' . $this->name2);
        }
        return $this->_dispname;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name1'], 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => array_keys(Person::getTypes())],
            [['name1', 'name2', 'yomi1', 'yomi2', 'role', 'contact_name1', 'contact_name2'], 'string', 'max' => 30],
            [['zip'], 'string', 'max' => 10],
            [['address1', 'address2', 'mail'], 'string', 'max' => 40],
            [['mail'], 'email'],
            [['phone1', 'phone2'], 'string', 'max' => 20],
            [['person_note', 'contact_note'], 'string', 'max' => 50],
            ['has_contact', 'boolean'],
            [['name1', 'name2'], function ($attribute, $param, $validator) {
                $person_count = 0;
                if (!$this->person) {
                    $person_count = Person::find()->where(['name' => $this->name1 . $this->name2])->count();
                } else {
                    $person_count = Person::find()->where(['<>', 'id', $this->person->id])
                        ->andWhere(['name' => $this->name1 . $this->name2])->count();
                }
                if ($person_count > 0) {
                    $this->addError('name1', '姓 および 名の "' . $this->name1 . '"-"'
                        . $this->name2 . '" という組み合わせは既に登録されています。');
                }
            }],
            [['role', 'contact_name1', 'contact_name2', 'zip', 'address1', 'address2', 'phone1', 'phone2', 'mail', 'contact_note'],
                'default', 'value' => ''],
            ['contact_note', 'required',
                'when' => function ($model) {
                    return ($model->has_contact && $model->role == '' && $model->contact_name1 == '' && $model->contact_name2 == ''
                        && $model->zip == '' && $model->address1 == '' && $model->address2 == ''
                        && $model->phone1 == '' && $model->phone2 == '' && $model->mail == '');
                },
                'whenClient' => "function (attribute, value) {
                    return ($('#has-contact').prop('checked') && !$('#role').val().length 
                    && !$('#contact-name1').val().length && !$('#contact-name2').val().length
                    && !$('#zip').val().length && !$('#address1').val().length && !$('#address2').val().length
                    && !$('#phone1').val().length && !$('#phone2').val().length && !$('#mail').val().length);
                }",
                'message' => '連絡先の全項目が空白です。どれも必須ではありませんが、一つは入力して下さい。'
            ],
            ['has_contact', 'required', 'requiredValue' => true,
                'when' => function ($model) {
                    return ($model->role != '' || $model->contact_name1 != '' || $model->contact_name2 != ''
                        || $model->zip != '' || $model->address1 != '' || $model->address2 != ''
                        || $model->phone1 != '' || $model->phone2 != '' || $model->mail != '' || $model->contact_note != '');
                },
                'whenClient' => "function (attribute, value) {
                    return ($('#role').val().length || $('#contact-name1').val().length || $('#contact-name2').val().length
                    || $('#zip').val().length || $('#address1').val().length || $('#address2').val().length
                    || $('#phone1').val().length || $('#phone2').val().length || $('#mail').val().length);
                }",
                'message' => '連絡先の項目が入力されています。「連絡先を登録・更新する」にチェックを入れて下さい。'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name1' => '氏／名称前半',
            'name2' => '名／名称後半',
            'yomi1' => 'よみがな（氏／名称前半）',
            'yomi2' => 'よみがな（名／名称後半）',
            'type' => 'タイプ',
            'person_note' => 'メモ',
            'has_contact' => '連絡先を登録・更新する',
            'role' => '役割／肩書',
            'contact_name1' => '連絡先名前半',
            'contact_name2' => '連絡先名後半',
            'zip' => '郵便番号',
            'address' => '住所',
            'address1' => '住所',
            'address2' => '住所（丁目・番地以降）',
            'phone1' => '電話（メイン）',
            'phone2' => '電話（その他）',
            'mail' => 'メール',
            'contact_note' => '連絡先メモ',
        ];
    }

    /**
     * @return boolean
     */
    public function savePersonAndContact()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->person == null) {
                $this->person = new Person();
            }
            $this->person->type = $this->type;
            $this->person->name1 = $this->name1;
            $this->person->name2 = $this->name2;
            $this->person->yomi1 = $this->yomi1;
            $this->person->yomi2 = $this->yomi2;
            $this->person->note = $this->person_note;
            if (!$this->person->save()) {
                Yii::error(['person_save_failed', $this->person->errors], __METHOD__);
                throw new \RuntimeException('Person save failed');
            }

            if ($this->has_contact) {
                if ($this->contact == null) {
                    $this->contact = new Contact();
                }
                $this->contact->person_id = $this->person->id;
                $this->contact->order = 1;
                $this->contact->role = $this->role;
                $this->contact->name1 = $this->contact_name1;
                $this->contact->name2 = $this->contact_name2;
                $this->contact->zip = $this->zip;
                $this->contact->address1 = $this->address1;
                $this->contact->address2 = $this->address2;
                $this->contact->phone1 = $this->phone1;
                $this->contact->phone2 = $this->phone2;
                $this->contact->note = $this->contact_note;
                if (!$this->contact->save()) {
                    Yii::error(['contact_save_failed', $this->contact->errors], __METHOD__);
                    throw new \RuntimeException('Contact save failed');
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e, __METHOD__);   // message だけより e 丸ごとが有益
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e, __METHOD__);   // message だけより e 丸ごとが有益
        }
        return false;
    }

    public function loadPersonAndContact($person_id)
    {
        $this->person = Person::findOne($person_id);
        if ($this->person === null) {
            throw new NotFoundHttpException('The requested person does not exist.');
        }

        $this->type = $this->person->type;
        $this->name1 = $this->person->name1;
        $this->name2 = $this->person->name2;
        $this->yomi1 = $this->person->yomi1;
        $this->yomi2 = $this->person->yomi2;
        $this->person_note = $this->person->note;

        if (count($this->person->contacts) > 0) {
            $this->has_contact = true;
            $this->contact = $this->person->contacts[0];
            $this->role = $this->contact->role;
            $this->contact_name1 = $this->contact->name1;
            $this->contact_name2 = $this->contact->name2;
            $this->zip = $this->contact->zip;
            $this->address1 = $this->contact->address1;
            $this->address2 = $this->contact->address2;
            $this->phone1 = $this->contact->phone1;
            $this->phone2 = $this->contact->phone2;
            $this->contact_note = $this->contact->note;
        } else {
            $this->has_contact = false;
        }
    }
}

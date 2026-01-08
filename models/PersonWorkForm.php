<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 *
 */
class PersonWorkForm extends Model
{
    public string $name1 = '';
    public string $name2 = '';
    public string $yomi1 = '';
    public string $yomi2 = '';
    public int $type = Person::TYPE_INDIVIDUAL;
    public string $person_note = '';

    public bool $has_contact = false;
    public string $zip = '';
    public string $address1 = '';
    public string $address2 = '';
    public string $contact_note = '';

    protected PersonWork $personWork;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['name1', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => array_keys(Person::getTypes())],
            [['name1', 'name2', 'yomi1', 'yomi2'], 'string', 'max' => 30],
            [['zip'], 'string', 'max' => 10],
            [['address1', 'address2'], 'string', 'max' => 40],
            [['person_note', 'contact_note'], 'string', 'max' => 50],
            ['has_contact', 'boolean'],
            [['name1', 'name2'], function($attribute, $param, $validator) {
                $persons = Person::findAll(['name' => $this->name1 . $this->name2]);
                if (count($persons) > 0) {
                    $this->addError('name1', '姓 および 名の "' . $this->name1 . '"-"'
                    . $this->name2 . '" という組み合わせは既に登録されています。');
                }
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name1' => '姓',
            'name2' => '名',
            'yomi1' => 'よみがな（姓）',
            'yomi2' => 'よみがな（名）',
            'type' => 'タイプ',
            'person_note' => 'メモ',
            'has_contact' => '連絡先も登録',
            'zip' => '郵便番号',
            'address' => '住所',
            'address1' => '住所',
            'address2' => '住所（続き）',
            'contact_note' => 'メモ',
        ];
    }

    public function readPersonWork($id)
    {
        $this->personWork = PersonWorkSearch::findOne($id);
        if ($this->personWork === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($this->personWork->person_id !== null) {
            $this->name1 = $this->personWork->person->name1;
            $this->name2 = $this->personWork->person->name2;
            $this->yomi1 = $this->personWork->person->yomi1;
            $this->yomi2 = $this->personWork->person->yomi2;
            $this->type = $this->personWork->person->type;
            $this->person_note = $this->personWork->person->note;
        } else {
            $this->setNames($this->personWork->name);
            if ($this->personWork->address !== null) {
                $this->has_contact = true;
                $this->setAddress($this->personWork->address);
            }
        }
    }

    private function setNames($name)
    {
        $name = str_replace('　', ' ', trim($name));
        $names = preg_split('/\s+/', $name);
        $this->name1 = $names[0];
        if (count($names) > 1) {
            $this->name2 = $names[1];
        }
    }

    private function setAddress($address)
    {
        $address = mb_convert_kana($address, 'asrn');
        if (strlen($address) == 0) {
            return;
        }
        // 「○○丁目」を拾って変換
        $address = preg_replace_callback(
            '/([一二三四五六七八九十]+)丁目/u',
            function ($m) {
                $kan = $m[1];

                $map = [
                    '一' => 1, '二' => 2, '三' => 3, '四' => 4, '五' => 5,
                    '六' => 6, '七' => 7, '八' => 8, '九' => 9,
                ];

                // 十の処理
                if ($kan === '十') {
                    $num = 10;
                } elseif (mb_strpos($kan, '十') !== false) {
                    [$a, $b] = explode('十', $kan, 2);
                    $num = ($a === '' ? 1 : $map[$a]) * 10
                        + ($b === '' ? 0 : $map[$b]);
                } else {
                    $num = $map[$kan] ?? $kan;
                }

                return $num . '丁目';
            },
            $address
        );
        $address1 = '';
        $address2 = '';
        // 最初の数字の位置で分ける
        if (preg_match('/\d/u', $address, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[0][1];
            $address1 = substr($address, 0, $pos);
            $address2 = substr($address, $pos);
        } else {
            $address1 = $address;
        }
        $this->address1 = $address1;
        $this->address2 = $address2;

        $this->searchZip($address1);
    }

    private function searchZip($address)
    {
        $base = 'https://tools.softark.net/zipdata/api/search';
        $qs = http_build_query([
            'callback' => 'cb',      // 何でもOK（JSONP用）
            'mode' => 1,
            'term' => $address,
            'max_rows' => 2,
            'biz_mode' => 0,
            'sort' => 0,
        ]);

        $url = $base . '?' . $qs;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FAILONERROR => true,
            CURLOPT_USERAGENT => 'zipdata-php/1.0',
        ]);
        $jsonp = curl_exec($ch);
        if ($jsonp === false) {
            $err = curl_error($ch);
            curl_close($ch);
            Yii::error("zip api request failed: {$err}");
            return;
        }
        curl_close($ch);

        // cb( ... ); を剥がして JSON にする
        if (!preg_match('/^[^(]*\((.*)\)\s*;?\s*$/s', $jsonp, $m)) {
            Yii::error("unexpected JSONP format");
            return;
        }

        $data = json_decode($m[1], true);
        if (!is_array($data)) {
            Yii::error("json_decode failed");
            return;
        }
        if (count($data) == 0) {
            Yii::warning("no candidates for $address");
            return;
        }
        if (count($data) > 1) {
            Yii::warning("multiple candidates for $address");
            return;
        }
        $this->zip = $data[0]['zip_code'];
        $this->address1 = $data[0]['pref'] . $data[0]['town'] . $data[0]['block'];
    }

    /**
     * @return boolean
     */
    public function register()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $person = new Person();
            $person->type = $this->type;
            $person->name1 = $this->name1;
            $person->name2 = $this->name2;
            $person->yomi1 = $this->yomi1;
            $person->yomi2 = $this->yomi2;
            $person->note = $this->person_note;
            if (!$person->save()) {
                Yii::error(['person_save_failed', $person->errors], __METHOD__);
                throw new \RuntimeException('Person save failed');
            }
            $this->personWork->person_id = $person->id;

            if ($this->has_contact) {
                $contact = new Contact();
                $contact->person_id = $person->id;
                $contact->name1 = $person->name1;
                $contact->name2 = $person->name2;
                $contact->zip = $this->zip;
                $contact->address1 = $this->address1;
                $contact->address2 = $this->address2;
                $contact->note = $this->contact_note;
                if (!$contact->save()) {
                    Yii::error(['contact_save_failed', $contact->errors], __METHOD__);
                    throw new \RuntimeException('Contact save failed');
                }
            }
            if (!$this->personWork->save()){
                Yii::error(['contact_save_failed', $this->personWork->errors], __METHOD__);
                throw new \RuntimeException('PersonWork save failed');
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
}

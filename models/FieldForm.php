<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class FieldForm extends Model
{
    public Field $field;

    public array $ofps;

    public array $cfps;

    public array $fus;

    public $new_ofp = false;

    public $new_cfp = false;

    public $new_fu = false;

    public function rules()
    {
        return [
            [['new_ofp', 'new_cfp', 'new_fu'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'new_ofp' => '新しい所有者を登録する',
            'new_cfp' => '新しい耕作者を登録する',
            'new_fu' => '新しい利用状況を登録する',
        ];
    }

    public function loadModels($field_id)
    {
        $this->field = Field::findOne($field_id);
        if (!$this->field) {
            throw new NotFoundHttpException('The requested Field data does not exist.');
        }
        $this->ofps = FieldPerson::find()
            ->where(['field_id' => $field_id, 'role' => FieldPerson::ROLE_OWNER])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->ofps[] = new FieldPerson(['field_id' => $field_id, 'role' => FieldPerson::ROLE_OWNER]);
        $this->cfps = FieldPerson::find()
            ->where(['field_id' => $field_id, 'role' => FieldPerson::ROLE_CULTIVATOR])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->cfps[] = new FieldPerson(['field_id' => $field_id, 'role' => FieldPerson::ROLE_CULTIVATOR]);
        $this->fus = FieldUsage::find()
            ->where(['field_id' => $field_id])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->fus[] = new FieldUsage(['field_id' => $field_id]);
    }

    public function loadPost($mode, $params)
    {
        if ($mode == 'f') {
            return $this->field->load($params);
        }
        if (!$this->load($params)) {
            return false;
        }
        if ($mode == 'o') {
            return FieldPerson::loadMultiple($this->ofps, $params);
        } elseif ($mode == 'c') {
            return FieldPerson::loadMultiple($this->cfps, $params);
        } elseif ($mode == 'u') {
            return FieldUsage::loadMultiple($this->fus, $params);
        }
        return false;
    }

    public function saveModels($mode)
    {
        if ($mode == 'f') {
            return $this->field->save();
        } elseif ($mode == 'o') {
            return $this->saveFps($this->ofps, $this->new_ofp);
        } elseif ($mode == 'c') {
            return $this->saveFps($this->cfps, $this->new_cfp);
        } elseif ($mode == 'u') {
            return $this->saveFus($this->fus, $this->new_fu);
        }
        return false;
    }

    protected function saveFps($fieldPersons, $new_fp)
    {
        $count = count($fieldPersons);
        if (!$new_fp) {
            $count--;
        }

        // 通常のバリデーション
        $ret = true;
        for ($i = 0; $i < $count; $i++) {
            $ret &= $fieldPersons[$i]->validate();
        }
        if (!$ret) {
            return false;
        }

        // valid_from の重なりを検証
        if ($count > 1) {
            for ($i = 1; $i < $count; $i++) {
                if ($fieldPersons[$i]->valid_from <= $fieldPersons[$i - 1]->valid_from) {
                    $fieldPersons[$i]->addError('valid_from', "FROM には、前の名義人の FROM ({$fieldPersons[$i-1]->valid_from}) より新しい日付を指定して下さい。");
                    $ret = false;
                }
            }
        }
        if (!$ret) {
            return false;
        }

        // valid_to を調整
        if ($count > 1) {
            for ($i = 0; $i < $count - 1; $i++) {
                $fieldPersons[$i]->valid_to = $fieldPersons[$i + 1]->valid_from;
            }
        }

        // save
        $tr = Yii::$app->db->beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
                $ret = $fieldPersons[$i]->save(false);
            }
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            throw $e;
        }
        return $ret;
    }

    protected function saveFus($fieldUsages, $new_fu)
    {
        $count = count($fieldUsages);
        if (!$new_fu) {
            $count--;
        }

        // 通常のバリデーション
        $ret = true;
        for ($i = 0; $i < $count; $i++) {
            $ret &= $fieldUsages[$i]->validate();
        }
        if (!$ret) {
            return false;
        }

        // valid_from の重なりを検証
        if ($count > 1) {
            for ($i = 1; $i < $count; $i++) {
                if ($fieldUsages[$i]->valid_from <= $fieldUsages[$i - 1]->valid_from) {
                    $fieldUsages[$i]->addError('valid_from', "FROM には、前の利用状況の FROM ({$fieldUsages[$i-1]->valid_from}) より新しい日付を指定して下さい。");
                    $ret = false;
                }
            }
        }
        if (!$ret) {
            return false;
        }

        // valid_to を調整
        if ($count > 1) {
            for ($i = 0; $i < $count - 1; $i++) {
                $fieldUsages[$i]->valid_to = $fieldUsages[$i + 1]->valid_from;
            }
        }

        // save
        $tr = Yii::$app->db->beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
                $ret = $fieldUsages[$i]->save(false);
            }
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            throw $e;
        }
        return $ret;
    }
}
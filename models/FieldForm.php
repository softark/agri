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

    public array $chfps;

    public array $safps;

    public array $fus;

    public $new_ofp = false;

    public $new_cfp = false;

    public $new_chfp = false;

    public $new_safp = false;

    public $new_fu = false;

    public function rules()
    {
        return [
            [['new_ofp', 'new_cfp', 'new_chfp', 'new_safp', 'new_fu'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'new_ofp' => '新しい所有者を登録する',
            'new_cfp' => '新しい耕作者を登録する',
            'new_chfp' => '新しい中山間名義人を登録する',
            'new_safp' => '新しい細目書名義人を登録する',
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
        foreach ($this->ofps as $ofp) {
            $ofp->setFormName('ofp');
        }
        if (count($this->ofps) == 1) {
            $this->ofps[0]->valid_from = '1900-01-01';
        }

        $this->cfps = FieldPerson::find()
            ->where(['field_id' => $field_id, 'role' => FieldPerson::ROLE_CULTIVATOR])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->cfps[] = new FieldPerson(['field_id' => $field_id, 'role' => FieldPerson::ROLE_CULTIVATOR]);
        foreach ($this->cfps as $cfp) {
            $cfp->setFormName('cfp');
        }
        if (count($this->cfps) == 1) {
            $this->cfps[0]->valid_from = '1900-01-01';
        }

        $this->chfps = FieldPerson::find()
            ->where(['field_id' => $field_id, 'role' => FieldPerson::ROLE_CHUSANKAN])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->chfps[] = new FieldPerson(['field_id' => $field_id, 'role' => FieldPerson::ROLE_CHUSANKAN]);
        foreach ($this->chfps as $chfp) {
            $chfp->setFormName('chfp');
        }
        if (count($this->chfps) == 1) {
            $this->chfps[0]->valid_from = '1900-01-01';
        }

        $this->safps = FieldPerson::find()
            ->where(['field_id' => $field_id, 'role' => FieldPerson::ROLE_SAIMOKUSHO])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->safps[] = new FieldPerson(['field_id' => $field_id, 'role' => FieldPerson::ROLE_SAIMOKUSHO]);
        foreach ($this->safps as $safp) {
            $safp->setFormName('safp');
        }
        if (count($this->safps) == 1) {
            $this->safps[0]->valid_from = '1900-01-01';
        }

        $this->fus = FieldUsage::find()
            ->where(['field_id' => $field_id])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->fus[] = new FieldUsage(['field_id' => $field_id]);
        if (count($this->fus) == 1) {
            $this->fus[0]->valid_from = '1900-01-01';
        }
    }

    public function loadPost($params)
    {
        $ret = $this->field->load($params);
        $ret &= FieldPerson::loadMultiple($this->ofps, $params);
        $ret &= FieldPerson::loadMultiple($this->cfps, $params);
        $ret &= FieldPerson::loadMultiple($this->chfps, $params);
        $ret &= FieldPerson::loadMultiple($this->safps, $params);
        $ret &= FieldUsage::loadMultiple($this->fus, $params);
        return $ret;
    }

    public function validateModels()
    {
        $ret = $this->field->validate();
        $ret &= $this->validateFps($this->ofps, $this->new_ofp);
        $ret &= $this->validateFps($this->cfps, $this->new_cfp);
        $ret &= $this->validateFps($this->chfps, $this->new_chfp);
        $ret &= $this->validateFps($this->safps, $this->new_safp);
        $ret &= $this->validateFus($this->fus, $this->new_fu);
        return $ret;
    }

    public function saveModels()
    {
        $tr = Yii::$app->db->beginTransaction();
        try {
            $this->field->save(false);
            $this->saveFps($this->ofps, $this->new_ofp);
            $this->saveFps($this->cfps, $this->new_cfp);
            $this->saveFps($this->chfps, $this->new_chfp);
            $this->saveFps($this->safps, $this->new_safp);
            $this->saveFus($this->fus, $this->new_fu);
            $tr->commit();
        }
        catch (Exception $e) {
            $tr->rollBack();
            throw $e;
        }
        return true;
    }

    protected function validateFps($fieldPersons, $new_fp)
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
        if ($ret) {
            // valid_to を調整
            if ($count > 1) {
                for ($i = 0; $i < $count - 1; $i++) {
                    $fieldPersons[$i]->valid_to = $fieldPersons[$i + 1]->valid_from;
                }
            }
        }
        return $ret;
    }

    protected function saveFps($fieldPersons, $new_fp)
    {
        $count = count($fieldPersons);
        if (!$new_fp) {
            $count--;
        }

        // save
        for ($i = 0; $i < $count; $i++) {
            $fieldPersons[$i]->save(false);
        }
    }

    protected function validateFus($fieldUsages, $new_fu)
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
        if ($ret) {
            // valid_to を調整
            if ($count > 1) {
                for ($i = 0; $i < $count - 1; $i++) {
                    $fieldUsages[$i]->valid_to = $fieldUsages[$i + 1]->valid_from;
                }
            }
        }

        return $ret;
    }

    protected function saveFus($fieldUsages, $new_fu)
    {
        $count = count($fieldUsages);
        if (!$new_fu) {
            $count--;
        }

        // save
        for ($i = 0; $i < $count; $i++) {
            $fieldUsages[$i]->save(false);
        }
    }
}
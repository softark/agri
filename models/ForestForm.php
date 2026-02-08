<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class ForestForm extends Model
{
    public Forest $forest;

    public array $ofps;

    public array $mfps;

    public $new_ofp = false;

    public $new_mfp = false;

    public function rules()
    {
        return [
            [['new_ofp', 'new_mfp'], 'boolean'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'new_ofp' => '新しい所有者を登録する',
            'new_mfp' => '新しい管理者を登録する',
        ];
    }

    public function loadModels($forest_id)
    {
        $this->forest = Forest::findOne($forest_id);
        if (!$this->forest) {
            throw new NotFoundHttpException('The requested Forest data does not exist.');
        }
        
        $this->ofps = ForestPerson::find()
            ->where(['forest_id' => $forest_id, 'role' => ForestPerson::ROLE_OWNER])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->ofps[] = new ForestPerson(['forest_id' => $forest_id, 'role' => ForestPerson::ROLE_OWNER]);
        foreach ($this->ofps as $ofp) {
            $ofp->setFormName('ofp');
        }
        if (count($this->ofps) == 1) {
            $this->ofps[0]->valid_from = '1900-01-01';
        }
        
        $this->mfps = ForestPerson::find()
            ->where(['forest_id' => $forest_id, 'role' => ForestPerson::ROLE_MANAGER])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->mfps[] = new ForestPerson(['forest_id' => $forest_id, 'role' => ForestPerson::ROLE_MANAGER]);
        foreach ($this->mfps as $mfp) {
            $mfp->setFormName('mfp');
        }
        if (count($this->mfps) == 1) {
            $this->mfps[0]->valid_from = '1900-01-01';
        }
    }

    public function loadPost($params)
    {
        $ret = $this->load($params);
        $ret &= $this->forest->load($params);
        $ret &= ForestPerson::loadMultiple($this->ofps, $params);
        $ret &= ForestPerson::loadMultiple($this->mfps, $params);
        return $ret;
    }

    public function validateModels()
    {
        $ret = $this->forest->validate();
        $ret &= $this->validateFps($this->ofps, $this->new_ofp);
        $ret &= $this->validateFps($this->mfps, $this->new_mfp);
        return $ret;
    }

    public function saveModels($mode)
    {
        $tr = Yii::$app->db->beginTransaction();
        try {
            $this->forest->save(false);
            $this->saveFps($this->ofps, $this->new_ofp);
            $this->saveFps($this->mfps, $this->new_mfp);
            $tr->commit();
        }
        catch (Exception $e) {
            $tr->rollBack();
            throw $e;
        }
        return true;
    }

    protected function validateFps($forestPersons, $new_fp)
    {
        $count = count($forestPersons);
        if (!$new_fp) {
            $count--;
        }

        // 通常のバリデーション
        $ret = true;
        for ($i = 0; $i < $count; $i++) {
            $ret &= $forestPersons[$i]->validate();
        }
        if (!$ret) {
            return false;
        }

        // valid_from の重なりを検証
        if ($count > 1) {
            for ($i = 1; $i < $count; $i++) {
                if ($forestPersons[$i]->valid_from <= $forestPersons[$i - 1]->valid_from) {
                    $forestPersons[$i]->addError('valid_from', "FROM には、前の名義人の FROM ({$forestPersons[$i-1]->valid_from}) より新しい日付を指定して下さい。");
                    $ret = false;
                }
            }
        }
        if ($ret) {
            // valid_to を調整
            if ($count > 1) {
                for ($i = 0; $i < $count - 1; $i++) {
                    $forestPersons[$i]->valid_to = $forestPersons[$i + 1]->valid_from;
                }
            }
        }
        return $ret;
    }

    protected function saveFps($forestPersons, $new_fp)
    {
        $count = count($forestPersons);
        if (!$new_fp) {
            $count--;
        }

        // save
        for ($i = 0; $i < $count; $i++) {
            $forestPersons[$i]->save(false);
        }
    }
}
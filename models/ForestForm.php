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
        $this->mfps = ForestPerson::find()
            ->where(['forest_id' => $forest_id, 'role' => ForestPerson::ROLE_MANAGER])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();
        $this->mfps[] = new ForestPerson(['forest_id' => $forest_id, 'role' => ForestPerson::ROLE_MANAGER]);
    }

    public function loadPost($mode, $params)
    {
        if ($mode == 'f') {
            return $this->forest->load($params);
        }
        if (!$this->load($params)) {
            return false;
        }
        if ($mode == 'o') {
            return ForestPerson::loadMultiple($this->ofps, $params);
        } elseif ($mode == 'm') {
            return ForestPerson::loadMultiple($this->mfps, $params);
        }
        return false;
    }

    public function saveModels($mode)
    {
        if ($mode == 'f') {
            return $this->forest->save();
        } elseif ($mode == 'o') {
            return $this->saveFps($this->ofps, $this->new_ofp);
        } elseif ($mode == 'm') {
            return $this->saveFps($this->mfps, $this->new_mfp);
        }
        return false;
    }

    protected function saveFps($forestPersons, $new_fp)
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
        if (!$ret) {
            return false;
        }

        // valid_to を調整
        if ($count > 1) {
            for ($i = 0; $i < $count - 1; $i++) {
                $forestPersons[$i]->valid_to = $forestPersons[$i + 1]->valid_from;
            }
        }

        // save
        $tr = Yii::$app->db->beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
                $ret = $forestPersons[$i]->save(false);
            }
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            throw $e;
        }
        return $ret;
    }
}
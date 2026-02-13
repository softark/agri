<?php

namespace app\models;

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use yii\data\ActiveDataProvider;

class ForestExcel
{
    /**
     * @param $dataProvider ActiveDataProvider
     * @throws \Exception;
     */
    public static function exportForestList($dataProvider)
    {
        /* @var $models Forest[] */
        $models = $dataProvider->models;

        // テンプレートをロード
        $template = Yii::getAlias('@app/models/data/forest.xlsx');
        $objSheet = IOFactory::load($template);
        // シート
        $sheet = $objSheet->getActiveSheet();
        // コンテント行を準備する
        SpreadsheetUtil::makeContentRowsReady($sheet, count($models));

        $header_row = 1;

        $row = $header_row;
        foreach ($models as $model) {
            /* @var $model Forest */
            $row++;
            $sheet->getCell('A' . $row)->setValueExplicit($row - 1, DataType::TYPE_NUMERIC);
            $sheet->getCell('B' . $row)->setValueExplicit($model->aza_name, DataType::TYPE_STRING);
            $sheet->getCell('C' . $row)->setValueExplicit($model->p_no, DataType::TYPE_STRING);
            $sheet->getCell('D' . $row)->setValueExplicit($model->type_name, DataType::TYPE_STRING);
            $sheet->getCell('E' . $row)->setValueExplicit((float)($model->area), DataType::TYPE_NUMERIC);
            $sheet->getCell('F' . $row)->setValueExplicit("=E$row / 100", DataType::TYPE_FORMULA);
            $sheet->getCell('G' . $row)->setValueExplicit("=E$row / 10000", DataType::TYPE_FORMULA);
            if ($model->owner) {
                $sheet->getCell('H' . $row)->setValueExplicit($model->owner->typetext, DataType::TYPE_STRING);
                $sheet->getCell('I' . $row)->setValueExplicit($model->owner->dispname, DataType::TYPE_STRING);
                if ($model->owner->contact) {
                    $sheet->getCell('J' . $row)->setValueExplicit($model->owner->contact->fullAddress, DataType::TYPE_STRING);
                    $sheet->getCell('K' . $row)->setValueExplicit($model->owner->contact->phones, DataType::TYPE_STRING);
                }
            }
            if ($model->manager) {
                $sheet->getCell('L' . $row)->setValueExplicit($model->manager->dispname, DataType::TYPE_STRING);
                if ($model->owner->contact) {
                    $sheet->getCell('M' . $row)->setValueExplicit($model->manager->contact->fullAddress, DataType::TYPE_STRING);
                    $sheet->getCell('N' . $row)->setValueExplicit($model->manager->contact->phones, DataType::TYPE_STRING);
                }
            }
            $sheet->getCell('O' . $row)->setValueExplicit($model->note, DataType::TYPE_STRING);
            $sheet->getCell('P' . $row)->setValueExplicit('i-GIS で見る', DataType::TYPE_STRING)
                ->getHyperlink()->setUrl($model->mapurl);
        }

        $sheet->getCell('E' . $row + 1)->setValueExplicit("=subtotal(109, E2:E$row)", DataType::TYPE_FORMULA);

        $ids = ForestSearch::getModelIds($dataProvider);
        $bbox = ForestSearch::getBboxTotal($ids);
        $selectionUrl = Forest::getSelectionMapUrl($ids, $bbox);

        $sheet->getCell('P' . $row + 1)->setValueExplicit('i-GIS で見る', DataType::TYPE_STRING)
            ->getHyperlink()->setUrl($selectionUrl);

        // 選択状態をリセット
        $sheet->setSelectedCell('A1');

        // 保存
        $fname = "岩座神山林-" . date('Y-m-d');
        SpreadsheetUtil::SaveForDownload($objSheet, $fname, SpreadsheetUtil::FILE_TYPE_EXCEL2007);
    }
}
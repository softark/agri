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

class FieldExcel
{
    /**
     * @param $dataProvider ActiveDataProvider
     * @throws \Exception;
     */
    public static function exportFieldList($dataProvider)
    {
        /* @var $models Field[] */
        $models = $dataProvider->models;

        // テンプレートをロード
        $template = Yii::getAlias('@app/models/data/field.xlsx');
        $objSheet = IOFactory::load($template);
        // シート
        $sheet = $objSheet->getActiveSheet();
        // コンテント行を準備する
        SpreadsheetUtil::makeContentRowsReady($sheet, count($models));

        $header_row = 1;

        $row = $header_row;
        foreach ($models as $model) {
            /* @var $model Field */
            $row++;
            $sheet->getCell('A' . $row)->setValueExplicit($row - 1, DataType::TYPE_NUMERIC);
            $sheet->getCell('B' . $row)->setValueExplicit($model->aza_name, DataType::TYPE_STRING);
            $sheet->getCell('C' . $row)->setValueExplicit($model->p_no, DataType::TYPE_STRING);
            $sheet->getCell('D' . $row)->setValueExplicit((float)($model->f_area), DataType::TYPE_NUMERIC);
            $sheet->getCell('E' . $row)->setValueExplicit("=D$row / 100", DataType::TYPE_FORMULA);
            $sheet->getCell('F' . $row)->setValueExplicit((float)($model->c_area), DataType::TYPE_NUMERIC);
            $sheet->getCell('G' . $row)->setValueExplicit("=F$row / 100", DataType::TYPE_FORMULA);
            $sheet->getCell('H' . $row)->setValueExplicit($model->owner_name, DataType::TYPE_STRING);
            $sheet->getCell('I' . $row)->setValueExplicit($model->cultivator_name, DataType::TYPE_STRING);
            $sheet->getCell('J' . $row)->setValueExplicit($model->chusankan_name, DataType::TYPE_STRING);
            $sheet->getCell('K' . $row)->setValueExplicit($model->saimokusho_name, DataType::TYPE_STRING);
            $sheet->getCell('L' . $row)->setValueExplicit($model->usage_name, DataType::TYPE_STRING);
            $sheet->getCell('M' . $row)->setValueExplicit($model->note, DataType::TYPE_STRING);
            $sheet->getCell('N' . $row)->setValueExplicit('i-GIS で見る', DataType::TYPE_STRING)
                ->getHyperlink()->setUrl($model->mapurl);
        }

        $sheet->getCell('D' . $row + 1)->setValueExplicit("=subtotal(109, D2:D$row)", DataType::TYPE_FORMULA);
        $sheet->getCell('F' . $row + 1)->setValueExplicit("=subtotal(109, F2:F$row)", DataType::TYPE_FORMULA);

        // 選択状態をリセット
        $sheet->setSelectedCell('A1');

        // 保存
        $fname = "岩座神農地-" . date('Y-m-d');
        SpreadsheetUtil::SaveForDownload($objSheet, $fname, SpreadsheetUtil::FILE_TYPE_EXCEL2007);
    }
}
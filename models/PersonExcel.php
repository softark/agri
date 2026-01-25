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

class PersonExcel
{
    /**
     * @param $dataProvider ActiveDataProvider
     * @throws \Exception;
     */
    public static function exportPersonList($dataProvider)
    {
        /* @var $models Person[] */
        $models = $dataProvider->models;

        // テンプレートをロード
        $template = Yii::getAlias('@app/models/data/person.xlsx');
        $objSheet = IOFactory::load($template);
        // シート
        $sheet = $objSheet->getActiveSheet();
        // コンテント行を準備する
        $row_count = 0;
        foreach ($models as $model) {
            $row_count++;
            if (count($model->contacts) > 1) {
                $row_count += count($model->contacts) - 1;
            }
        }
        SpreadsheetUtil::makeContentRowsReady($sheet, $row_count);

        $header_row = 1;

        $row = $header_row;
        foreach ($models as $model) {
            /* @var $model Person */
            $row++;
            $sheet->getCell('A' . $row)->setValueExplicit($row - 1, DataType::TYPE_NUMERIC);
            $sheet->getCell('B' . $row)->setValueExplicit($model->num_fields, DataType::TYPE_NUMERIC);
            $sheet->getCell('C' . $row)->setValueExplicit($model->num_forests, DataType::TYPE_NUMERIC);
            $sheet->getCell('D' . $row)->setValueExplicit($model->typeText, DataType::TYPE_STRING);
            $sheet->getCell('E' . $row)->setValueExplicit($model->dispname, DataType::TYPE_STRING);
            $sheet->getCell('F' . $row)->setValueExplicit($model->note, DataType::TYPE_STRING);
            if (count($model->contacts) > 0) {
                $row--;
                foreach ($model->contacts as $contact) {
                    $row++;
                    $sheet->getCell('G' . $row)->setValueExplicit($contact->contact_name, DataType::TYPE_STRING);
                    $sheet->getCell('H' . $row)->setValueExplicit($contact->zip, DataType::TYPE_STRING);
                    $sheet->getCell('I' . $row)->setValueExplicit($contact->address1 . $contact->address2, DataType::TYPE_STRING);
                    $sheet->getCell('J' . $row)->setValueExplicit($contact->phone1, DataType::TYPE_STRING);
                    $sheet->getCell('K' . $row)->setValueExplicit($contact->phone2, DataType::TYPE_STRING);
                    $sheet->getCell('L' . $row)->setValueExplicit($contact->mail, DataType::TYPE_STRING);
                    $sheet->getCell('M' . $row)->setValueExplicit($contact->note, DataType::TYPE_STRING);
                }
            }
        }

        // 選択状態をリセット
        $sheet->setSelectedCell('A1');

        // 保存
        $fname = "関係者名簿-" . date('Y-m-d');
        SpreadsheetUtil::SaveForDownload($objSheet, $fname, SpreadsheetUtil::FILE_TYPE_EXCEL2007);
    }
}
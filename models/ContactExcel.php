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

class ContactExcel
{
    /**
     * @param $dataProvider ActiveDataProvider
     * @throws \Exception;
     */
    public static function exportContactList($dataProvider)
    {
        /* @var $models Contact[] */
        $models = $dataProvider->models;

        // テンプレートをロード
        $template = Yii::getAlias('@app/models/data/contact.xlsx');
        $objSheet = IOFactory::load($template);
        // シート
        $sheet = $objSheet->getActiveSheet();
        // コンテント行を準備する
        SpreadsheetUtil::makeContentRowsReady($sheet, count($models));

        $header_row = 1;

        $row = $header_row;
        $p_row = 0;
        foreach ($models as $model) {
            /* @var $model Contact */
            $row++;
            $sheet->getCell('A' . $row)->setValueExplicit($row - 1, DataType::TYPE_NUMERIC);
            $sheet->getCell('B' . $row)->setValueExplicit($model->person->typeText, DataType::TYPE_STRING);
            $sheet->getCell('C' . $row)->setValueExplicit($model->person->name1, DataType::TYPE_STRING);
            $sheet->getCell('D' . $row)->setValueExplicit($model->person->name2, DataType::TYPE_STRING);
            $sheet->getCell('E' . $row)->setValueExplicit($model->person->yomi1, DataType::TYPE_STRING);
            $sheet->getCell('F' . $row)->setValueExplicit($model->person->yomi2, DataType::TYPE_STRING);
            $sheet->getCell('G' . $row)->setValueExplicit($model->person->note, DataType::TYPE_STRING);
            $sheet->getCell('H' . $row)->setValueExplicit($model->order . ' / ' . count($model->person->contacts), DataType::TYPE_STRING);
            $sheet->getCell('I' . $row)->setValueExplicit($model->role, DataType::TYPE_STRING);
            $sheet->getCell('J' . $row)->setValueExplicit($model->name1, DataType::TYPE_STRING);
            $sheet->getCell('K' . $row)->setValueExplicit($model->name2, DataType::TYPE_STRING);
            $sheet->getCell('L' . $row)->setValueExplicit($model->zip, DataType::TYPE_STRING);
            $sheet->getCell('M' . $row)->setValueExplicit($model->address1, DataType::TYPE_STRING);
            $sheet->getCell('N' . $row)->setValueExplicit($model->address2, DataType::TYPE_STRING);
            $sheet->getCell('O' . $row)->setValueExplicit($model->phone1, DataType::TYPE_STRING);
            $sheet->getCell('P' . $row)->setValueExplicit($model->phone2, DataType::TYPE_STRING);
            $sheet->getCell('Q' . $row)->setValueExplicit($model->mail, DataType::TYPE_STRING);
            $sheet->getCell('R' . $row)->setValueExplicit($model->note, DataType::TYPE_STRING);
        }

        // 選択状態をリセット
        $sheet->setSelectedCell('A1');

        // 保存
        $fname = "連絡先-" . date('Y-m-d');
        SpreadsheetUtil::SaveForDownload($objSheet, $fname, SpreadsheetUtil::FILE_TYPE_EXCEL2007);
    }
}
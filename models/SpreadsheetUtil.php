<?php
/**
 * Created by PhpStorm.
 * User: kihar_000
 * Date: 2015/07/19
 * Time: 12:55
 */

namespace app\models;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;


class SpreadsheetUtil
{
    const FILE_TYPE_EXCEL2007 = 'Xlsx';
    const FILE_TYPE_EXCEL5 = 'Xls';
    const FILE_TYPE_CSV = 'Csv';
    const FILE_TYPE_PDF = 'Pdf';

    /**
     * 範囲を示す文字列を返す
     * @param string $col
     * @param string $row
     * @return string
     */
    static function CellByStr($col, $row)
    {
        return $col . $row;
    }

    /**
     * 範囲を示す文字列を返す
     * @param integer $col
     * @param integer $row
     * @return string
     */
    static function CellByIndex($col, $row)
    {
        return Coordinate::stringFromColumnIndex($col) . $row;
    }

    /**
     * 範囲を示す文字列を返す
     * @param string $col_s
     * @param string $row_s
     * @param string $col_e
     * @param string $row_e
     * @return string
     */
    static function RangeByStr($col_s, $row_s, $col_e, $row_e)
    {
        return self::CellByStr($col_s, $row_s) . ':' . self::CellByStr($col_e, $row_e);
    }

    /**
     * 範囲を示す文字列を返す
     * @param integer $col_s
     * @param integer $row_s
     * @param integer $col_e
     * @param integer $row_e
     * @return string
     */
    static function RangeByIndex($col_s, $row_s, $col_e, $row_e)
    {
        return self::CellByIndex($col_s, $row_s) . ':' . self::CellByIndex($col_e, $row_e);
    }

    /**
     * ヘッダのスタイルを適用する
     * @param Worksheet $sheet
     * @param string $range
     * @throws  \Exception;
     */
    static function ApplyHeaderStyle($sheet, $range)
    {
        $borderColor = 'FF808080';
        $headerColor = 'FFFFFFCC';
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => $borderColor),
                ),
            ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'startColor' => array('argb' => $headerColor),
            ),
        );

        $sheet->getStyle($range)->applyFromArray($styleArray);
    }

    /**
     * ボディーのスタイルを適用する
     * @param Worksheet $sheet
     * @param string $range
     * @throws  \Exception;
     */
    static function ApplyBodyStyle($sheet, $range)
    {
        $borderColor = 'FF808080';
        $styleArray = array(
            'alignment' => array(
                'vertical' => Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => $borderColor),
                ),
            ),
        );

        $sheet->getStyle($range)->applyFromArray($styleArray);
    }

    public static function makeContentRowsReady($sheet, $numRows, $startRow = 2, $endCol = '')
    {
        $startCol = 'A';
        if ($endCol == '') {
            $endCol = $sheet->getHighestColumn(); // 例: 'K'
        }

        $sheet->insertNewRowBefore($startRow + 1, $numRows - 1);

        // テンプレ行のスタイルを取得して、全行へ一括適用
        for ($col = $startCol; $col !== $endCol; $col++) {
            $style = $sheet->getStyle($col . $startRow);
            $sheet->duplicateStyle($style, $col . ($startRow + 1) . ':' . $col . ($startRow + $numRows - 1));
        }
        $style = $sheet->getStyle($endCol . $startRow);
        $sheet->duplicateStyle($style, $endCol . ($startRow + 1) . ':' . $endCol . ($startRow + $numRows - 1));

        Cell::setValueBinder(new AdvancedValueBinder());
    }

    /**
     * 日付文字列として取得する
     * @param Worksheet $sheet
     * @param string $cell
     * @return string
     * @throws  \Exception;
     */
    public static function GetAsDateStr($sheet, $cell)
    {
        $value = $sheet->getCell($cell)->getValue();
        return NumberFormat::toFormattedString($value, NumberFormat::FORMAT_DATE_YYYYMMDD2);
    }

    /**
     * ファイルに保存する
     * @param Spreadsheet $spreadsheet
     * @param string $dirname
     * @param string $filename
     * @param string $filetype
     * @throws Exception
     */
    static function SaveAsFile($spreadsheet, $dirname, $filename, $filetype = self::FILE_TYPE_EXCEL2007)
    {
        /* @var $objWriter IWriter */
        $objWriter = null;
        $content_type = '';
        $ext = '';

        self::getWriter($spreadsheet, $objWriter, $content_type, $ext, $filename, $filetype);

        // $objWriter->setIncludeCharts(true); // テンプレート経由でチャートを利用するのは非常に困難
        $objWriter->save($dirname . DIRECTORY_SEPARATOR . $filename . $ext);
    }

    /**
     * @param $spreadsheet Spreadsheet
     * @param $objWriter IWriter
     * @param $content_type string
     * @param $ext string
     * @param $filename string
     * @param $filetype string
     * @param $useChart boolean
     * @throws Exception
     */
    static private function getWriter($spreadsheet, &$objWriter, &$content_type, &$ext, &$filename, $filetype, $useChart = false)
    {
        if ($filetype == self::FILE_TYPE_EXCEL2007) {
            $objWriter = IOFactory::createWriter($spreadsheet, $filetype);
            /* @var $objWriter BaseWriter */
            $objWriter->setIncludeCharts($useChart);
            $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            $ext = '.xlsx';
        } elseif ($filetype == self::FILE_TYPE_EXCEL5) {
            $objWriter = IOFactory::createWriter($spreadsheet, $filetype);
            $content_type = 'application/vnd.ms-excel';
            $ext = '.xls';
        } elseif ($filetype == self::FILE_TYPE_CSV) {
            $objWriter = IOFactory::createWriter($spreadsheet, $filetype);
            $content_type = 'text/csv charset=Shift_JIS';
            $ext = '.csv';
        } elseif ($filetype == self::FILE_TYPE_PDF) {
            $objWriter = IOFactory::createWriter($spreadsheet, $filetype);
            $content_type = 'application/pdf';
            $ext = '.pdf';
        } else {
            throw new Exception('self::getWriter ... invalid file type');
        }

        // ファイル名で使えない文字 をアンダーバーにする
        // \ / : * ? " < > |
        $search = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
        $replace = array('_', '_', '_', '_', '_', '_', '_', '_', '_');
        $filename = str_replace($search, $replace, $filename);

        // 全角空白を半角空白に
        $filename = mb_convert_kana($filename, 's', 'UTF-8');

        // 半角空白をアンダーバーにする
        $filename = str_replace(' ', '_', $filename);
    }

    /**
     * ダウンロード用に出力として保存する
     * @param Spreadsheet $objPHPExcel
     * @param string $filename
     * @param string $filetype
     * @param boolean $useChart
     * @throws Exception
     */
    static function SaveForDownload($objPHPExcel, $filename, $filetype = self::FILE_TYPE_EXCEL2007, $useChart = false)
    {
        /* @var $objWriter IWriter */
        $objWriter = null;
        $content_type = '';
        $ext = '';

        // $filename .= date('-ymd-His');
        self::getWriter($objPHPExcel, $objWriter, $content_type, $ext, $filename, $filetype, $useChart);

        // ブラウザを判定する
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $browser = 'unknown';
        if (strstr($ua, 'Edge')) {
            $browser = 'edge';
        } elseif ((strstr($ua, 'MSIE') || strstr($ua, 'Trident')) && !strstr($ua, 'Opera')) {
            $browser = 'msie';
        } elseif (strstr($ua, 'Opera')) {
            $browser = 'opera';
        } elseif (strstr($ua, 'Firefox')) {
            $browser = 'firefox';
        } elseif (strstr($ua, "Chrome")) {
            $browser = 'chrome';
        } elseif (strstr($ua, "Safari")) {
            $browser = 'safari';
        }

        // 英数字だけかを判定する
        $ascii = mb_convert_encoding($filename, "US-ASCII", "UTF-8");
        if ($ascii == $filename) {
            $browser = 'ascii';
        }

        $is_rfc2231 = false;

        // ブラウザに応じた処理
        switch ($browser) {
            // urlencode する
            case 'ascii':
                $filename = rawurlencode($filename);
                break;

            // SJIS に変換する
            case 'msie':
            case 'edge':
                $filename = mb_convert_encoding($filename, "Shift_JIS", "UTF-8");
                break;

            // UTF-8 のまま
            case 'safari':
                break;
            // RFC2231形式を使用する
            case 'firefox':
            case 'chrome':
            case 'opera':
            default:
                $filename = "utf-8'ja'" . rawurlencode($filename);
                $is_rfc2231 = true;
                break;
        }

        $fname_frag = $is_rfc2231 ? 'filename*=' : 'filename=';

        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; ' . $fname_frag . $filename . $ext);
        header('Cache-Control: max-age=0');

        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-9-12
 * Time: 上午11:12
 */
class Excel
{
    /**
     *
     * $config = array(
     *      'charActors' => $charActors,
     *      'widthSize' => $widthSize,
     *      'titleName' => $titleName,
     * );
     *
     **/
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    function dump($excel, $filename)
    {
        $write = new PHPExcel_Writer_Excel2007($this->style($excel));
        header("Access-Control-Allow-Origin:*");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $filename . '.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');die();
    }

    public function style($excel)
    {
        //输出到浏览器
        foreach ($this->config['charActors'] as $k => $v) {
            $excel->getActiveSheet()->getStyle($v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //对齐方式，水平剧中
            $excel->getActiveSheet()->getColumnDimension($v)->setWidth($this->config['widthSize'][$k]); //设置表格宽度
            $excel->getActiveSheet()->getStyle($v)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格为文本
            $excel->getActiveSheet()->setCellValue($v . 1, $this->config['titleName'][$k]); //为单元格赋值
        }

        return $excel;
    }

    public function styleData($excel)
    {
        //输出到浏览器
        foreach ($this->config['charActors'] as $k => $v) {
            $excel->getActiveSheet()->getStyle($v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //对齐方式，水平剧中
            $excel->getActiveSheet()->getColumnDimension($v)->setWidth($this->config['widthSize'][$k]); //设置表格宽度
            $excel->getActiveSheet()->getStyle($v)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格为文本
            $excel->getActiveSheet()->setCellValue($v . 1, $this->config['titleName'][$k]); //为单元格赋值
        }

        return $excel;
    }

    public function import($files)
    {
        $inputFileName = $files['file_upload']['tmp_name'];
        try {
            $fileTypes = array('xls','xlsx'); // File extensions
            $fileParts = pathinfo($files['file_upload']['name']);

            if (!in_array(strtolower($fileParts['extension']), $fileTypes)) {
                throw new \Exception("EXCEL文件格式不符合要求(xls,xlsx);");
            }

            $inputFileType = IOFactory::identify($inputFileName);
            $objReader = IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

        } catch(Exception $e) {
            throw $e;
        }

        $data = array();
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData_sheet = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData_sheet[0][4] = str_replace(",", "", $rowData_sheet[0][4]); //去掉数字中的200,00,00.00
            array_push($data, $rowData_sheet[0]);
        }

        return $data;
    }
}
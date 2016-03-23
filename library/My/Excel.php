<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
require_once APPLICATION_PATH . '/../library/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';

class My_Excel
{

    /**
     *
     * @var type 
     */
    protected $_phpExcel;

    /**
     *
     * @var type 
     */
    protected $_currentRow;

    /**
     *
     * @var type 
     */
    protected $_startingColumn;

    /**
     *
     * @var type 
     */
    protected $_endingColumn;

    /**
     *
     * @var type 
     */
    protected $_activeSheet;

    /**
     *
     * @var string
     */
    public $_fileName = 'finny-report';

    /**
     * 
     */
    public function __construct()
    {
        $this->_phpExcel = new PHPExcel();
        $this->_activeSheet = $this->_phpExcel->getActiveSheet();
        $this->_currentRow = 1;
        $this->_endingColumn = $this->_startingColumn = 'B';
    }

    /**
     * 
     * @param type $data
     */
    public function arrayToExcel($data)
    {

        for ($count = 0; $count < count($data['table'][0]); $count++) {
            $this->_endingColumn++;
        }

        $this->_fileName = $data['header']['title'];
        $this->setTitle($data['header']);
        $this->tableToExcel(array('data' => $data['table']));

        if (isset($data['subtable'])) {
            foreach ($data['subtable'] as $subtable) {
                $this->tableToExcel($subtable);
            }
        }
    }

    /**
     * 
     * @param type $data
     * @return boolean
     */
    protected function tableToExcel($data)
    {

        if (empty($data['data'])) {
            return false;
        }

        $table = $data['data'];
        $this->_currentRow += 2;

        if (isset($data['title'])) {
            $this->setTableTitle($data['title']);
        }

        $startingColumn = $currentColumn = $this->_startingColumn;
        $startingRow = $this->_currentRow;

        for ($count = 1; $count < count($table[0]); $count++) {
            $currentColumn++;
        }

        $currentRow = $this->_currentRow++;
        $this->_activeSheet->fromArray($table[0], NULL, $this->_startingColumn . $currentRow);
        unset($table[0]);

        $headerFontStyleArray = array(
            'font' => array(
                'bold' => true
            )
        );

        $headerRange = $startingColumn . $startingRow . ':' . $currentColumn . $currentRow;
        $this->_activeSheet->getStyle($headerRange)->applyFromArray($headerFontStyleArray);

        $this->_activeSheet->fromArray($table, NULL, $startingColumn . $this->_currentRow);
        $this->_currentRow += count($table) - 1;

        $tableRange = $startingColumn . $startingRow . ':' . $currentColumn . $this->_currentRow;
        $this->_activeSheet->getStyle($tableRange)->applyFromArray($this->getBorderStyle());
        $this->_activeSheet->getStyle($tableRange)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->_activeSheet->getStyle($tableRange)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        return true;
    }

    protected function setTableTitle($title)
    {

        $titleStartColumn = $this->_startingColumn;
        $titleEndColumn = $this->_startingColumn;
        for ($count = 0; $count < 4; $count++) {
            $titleEndColumn++;
        }

        $this->_currentRow++;
        $titleRange = $titleStartColumn . $this->_currentRow . ':' . $titleEndColumn . $this->_currentRow;
        $this->_activeSheet->mergeCells($titleRange);
        $this->_activeSheet->setCellValue($titleStartColumn . $this->_currentRow, $title);

        $titleFontStyleArray = array(
            'font' => array(
                'bold' => true
            )
        );
        $this->_activeSheet->getStyle($titleRange)->applyFromArray($titleFontStyleArray);

        $this->_currentRow += 2;
    }

    /**
     * 
     * @param type $header
     */
    protected function setTitle($header)
    {
        $this->setHeaderImage();
        $this->_activeSheet->mergeCells('C1:H4');
        $this->_activeSheet->setCellValue('C1', $header['title']);

        $this->_activeSheet->mergeCells('B5:H6');
        $this->_activeSheet->setCellValue('B5', $header['kid']);

        $this->_currentRow = 6;
        if (!empty($header['extra'])) {
            foreach ($header['extra'] as $extra) {
                $this->_currentRow++;
                $this->_activeSheet->mergeCells('B' . $this->_currentRow . ':H' . $this->_currentRow);
                $this->_activeSheet->setCellValue('B' . $this->_currentRow, $extra);
            }
        }

        $fontStyleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 15,
                'name' => 'Verdana'
            )
        );

        $this->_activeSheet->getStyle('B1:H5')->applyFromArray($fontStyleArray);
        $this->_activeSheet->getStyle('B1:H' . $this->_currentRow)->applyFromArray($this->getBorderStyle());
        $this->_activeSheet->getStyle('B1:H' . $this->_currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->_activeSheet->getStyle('B1:H' . $this->_currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->_currentRow++;
    }

    /**
     * 
     */
    protected function setHeaderImage()
    {
        $this->_activeSheet->mergeCells('B1:B4');
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setWorksheet($this->_activeSheet);
        $objDrawing->setPath(APPLICATION_PATH . '/../public/images/logo.png');
        $objDrawing->setWidthAndHeight(60, 60);
        $objDrawing->setCoordinates('B1');
    }

    /**
     * 
     */
    protected function setAutoWidth()
    {

        $startingColumn = $this->_startingColumn;
        $startingColumn++;
        $endingColumn = ($this->_endingColumn < 'H') ? 'H' : $this->_endingColumn;
        for ($column = $startingColumn; $column != $endingColumn; $column++) {
            $this->_activeSheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    protected function setFooter()
    {
        $this->_currentRow += 4;
        $footerRange = 'B' . $this->_currentRow . ':H' . $this->_currentRow;
        $this->_activeSheet->mergeCells($footerRange);
        $this->_activeSheet->setCellValue('B' . $this->_currentRow, '© Copyright myfinny.com');

        $this->_activeSheet->getStyle($footerRange)->applyFromArray($this->getBorderStyle());
        $this->_activeSheet->getStyle($footerRange)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->_activeSheet->getStyle($footerRange)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }

    protected function getBorderStyle()
    {
        return array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
    }

    /**
     * 
     * @param type $filename
     */
    public function output()
    {
        $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '', preg_replace('/[\s]+/', '_', $this->_fileName));

        $this->setFooter();
        $this->setAutoWidth();
        $objWriter = PHPExcel_IOFactory::createWriter($this->_phpExcel, 'Excel2007');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx' . '"');
        $objWriter->save('php://output');
    }

}

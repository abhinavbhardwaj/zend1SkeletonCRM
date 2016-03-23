<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
require_once APPLICATION_PATH . '/../library/tcpdf/tcpdf.php';

class My_Pdf extends TCPDF
{

    /**
     *
     * @var Array
     */
    protected $_header;

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
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Ashwini Agarwal');
        $this->SetTitle('Finny Reports');
        $this->SetSubject('Finny Reports');

        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->setImageScale(1.75);

        libxml_use_internal_errors(true);
    }

    public function Header()
    {
        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/pdf/');
        $template->assign('data', $this->_header);
        $html = $template->render('header.phtml');
        $this->writeHTML($html, true, false, false, false, '');
    }

    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/pdf/');
        $template->assign('current', $this->getAliasNumPage());
        $template->assign('total', $this->getAliasNbPages());
        $html = $template->render('footer.phtml');
        $this->writeHTML($html, true, false, false, false, '');
    }

    /**
     * 
     * @param type $data
     */
    public function arrayToPdf($data)
    {
        $this->_header = $data['header'];
        $this->_fileName = $data['header']['title'];

        $this->AddPage();

        $this->tableToPdf(array('data' => $data['table'], 'width' => $this->processWidth($data['width'])));
        if (isset($data['subtable'])) {
            foreach ($data['subtable'] as $subtable) {
                $subtable['width'] = $this->processWidth($subtable['width']);
                $this->tableToPdf($subtable);
            }
        }
        $this->output();
    }

    public function tableToPdf($data)
    {
        if (empty($data['data'])) {
            return false;
        }

        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/pdf/');
        $template->assign('data', $data);
        $html = $template->render('table.phtml');
        $this->writeHTML($html, true, false, false, false, '');
    }

    public function output()
    {
        parent::Output($this->_fileName . '.pdf', 'D');
    }

    public function processWidth($widths)
    {
        $total = array_sum($widths);
        if ($total == 0) {
            return $widths;
        }
        foreach ($widths as &$value) {
            $value = ($value * 100) / $total;
            $value .= '%';
        }
        return $widths;
    }

}

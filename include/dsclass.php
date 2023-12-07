<?php

/**
 * @author - getjobworking@gmail.com
 */
class dsclass
{

    private $mpdf;

    private $smarty;

    private $h;

    private $w;

    private $date;

    private $docNum;

    private $docNumShort;

    private $barCode;

    private $convertedDate;

    private $configPath = 'config/config.json';

    private $pdfPath;

    private $tempDirectory;

    private $templateDirectory;

    private $templateCompileDirectory;

    private $fontDirectory;

    private $cssFile;

    private $fontRegular;

    private $fontBold;

    private $fontItalic;

    private $fontBoldItalic;

    private $header;

    private $applicationName;

    private $firstInfo;

    private $refersToInfo;

    private $email;

    private $contentTo;

    private $signature;

    private $signatureSummary;

    private $longNumHeader;

    private $shortNumHeader;

    private $institution;

    private $pdfPages;

    private $pageOf;

    private $folderChapterPaths = [];

    private $imgRightBottom;

    private $imgRightTop;

    private $imgLeftTop;

    private $author;

    private $creator;

    private $keywords;

    private $subject;

    private $title;

    public function __construct()
    {
        $this->loadConfig();
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(__path__ . $this->templateDirectory);
        $this->smarty->setCompileDir(__path__ . $this->templateCompileDirectory);
    }

    private function loadConfig()
    {
        $configJson = file_get_contents($this->configPath);
        $configArray = json_decode($configJson, true);

        if ($configArray === null) {
            throw new Exception('Błąd podczas wczytywania pliku konfiguracyjnego JSON.');
        }

        $this->tempDirectory = $configArray['tempDirectory'];
        $this->templateDirectory = $configArray['templateDirectory'];
        $this->templateCompileDirectory = $configArray['templateCompileDirectory'];
        $this->fontDirectory = $configArray['fontDirectory'];
        $this->cssFile = $configArray['cssFile'];
        $this->fontRegular = $configArray['fontRegular'];
        $this->fontBold = $configArray['fontBold'];
        $this->fontItalic = $configArray['fontItalic'];
        $this->fontBoldItalic = $configArray['fontBoldItalic'];
        $this->header = $configArray['header'];
        $this->applicationName = $configArray['applicationName'];
        $this->firstInfo = $configArray['firstInfo'];
        $this->refersToInfo = $configArray['refersToInfo'];
        $this->email = $configArray['email'];
        $this->signature = $configArray['signature'];
        $this->signatureSummary = $configArray['signatureSummary'];
        $this->longNumHeader = $configArray['longNumHeader'];
        $this->shortNumHeader = $configArray['shortNumHeader'];
        $this->institution = $configArray['institution'];
        $this->folderChapterPaths = $configArray['folderChapterPaths'];
        $this->contentTo = $configArray['contentTo'];
        $this->pdfPath = $configArray['pdfPath'];
        $this->pdfPages = $configArray['pdfPages'];
        $this->pageOf = $configArray['pageOf'];
        $this->imgRightBottom = $configArray['imgRightBottom'];
        $this->imgRightTop = $configArray['imgRightTop'];
        $this->imgLeftTop = $configArray['imgLeftTop'];
        $this->strlen = $configArray['strlen'];
        $this->count = $configArray['count'];
    }
    
    
    private function setMetadata(){
        $this->mpdf->SetAuthor($this->author);
        $this->mpdf->SetCreator($this->creator);
        $this->mpdf->SetKeywords($this->keywords);
        $this->mpdf->SetSubject($this->subject.$this->docNum);
        $this->mpdf->SetTitle($this->title.$this->docNum);
    }

    private function createMPdf()
    {
        $this->mpdf = new \Mpdf\Mpdf([
            'tempDir' => __path__ . $this->tempDirectory,
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'defaultPageNumStyle' => '1',
            'watermarkImgBehind' => true,
            'fontDir' => __path__ . $this->fontDirectory,
            'default_font' => 'andada',
            'fontdata' => [
                'andada' => [
                    'R' => $this->fontRegular,
                    'B' => $this->fontBold,
                    'I' => $this->fontItalic,
                    'BI' => $this->fontBoldItalic
                ]
            ]
        ]);
    }

    public function generatePDF()
    {
        $this->createMPdf();
        $styleSheet = file_get_contents($this->cssFile);
        $this->mpdf->WriteHTML($styleSheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $this->date = date("d.m.Y");
        $this->convertedDate = convertDateEng($this->date);
        $this->smarty->assign('nbpg', '{nbpg}');
        $this->smarty->assign('PAGENO', '{PAGENO}');
        $this->smarty->assign('MDATE', $this->date);

        $code = capitalFirstLetters($this->institution,6,3);

        $this->docNum = $this->getNumber($code);
        $this->docNumShort = $this->getNumber($code, false);

        $this->barCode = createBarCode($this->docNum);

        $this->mpdf->SetWatermarkImage(createImage($this->docNum), 0.2, 'F', 'F');
        $this->mpdf->showWatermarkImage = true;

        $this->mpdf->SetHTMLFooter($this->getFooterTemplate());
        $this->mpdf->SetHTMLHeader($this->getHeaderTemplate($this->header));
        $this->createPageWithMargins(10, 10, 20, 10);

        $this->smarty->assign('documentNumber', $this->getDocNumShortTemplate());
        $this->smarty->assign('documentDate', $this->getDataTemplate($this->date, $this->convertedDate));
        $this->smarty->assign('institutionName', $this->getInstitution());
        $this->smarty->assign('application', $this->getApplicationTemplate($this->applicationName));
        $this->smarty->assign('firstInfo', $this->getFirstInfoTemplate($this->firstInfo));
        $this->smarty->assign('email', $this->getEmailDomainTemplate(divideEmail($this->email)));

        $this->smarty->assign('refersTo', $this->getRefersToTemplate($this->refersToInfo));
        $this->smarty->assign('contentToInstitution', $this->getContentToTemplate($this->contentTo));

        $this->smarty->assign('signature', $this->getSignatureTemplate($this->signature, $this->signatureSummary));

        $this->smarty->assign('imagesTop', $this->getImagesTopTemplate());
        $this->smarty->assign('imagesBottom', $this->getImagesBottomTemplate());
        $this->smarty->assign('dataContent', file_get_contents('content/content.html'));

        $this->mpdf->WriteHTML($this->smarty->fetch('dsmain-page.tpl'), \Mpdf\HTMLParserMode::HTML_BODY, true, true);
        $file = $this->pdfPath . '/' . $this->docNum . '.pdf';
        $this->mpdf->Output($file, 'F');
        $this->mpdf->Output();
    }

    /* other functions */
    /**
     *
     * @param integer $left
     * @param integer $right
     * @param integer $top
     * @param integer $bottom
     * @return string rendered template output
     */
    public function createPageWithMargins($left, $right, $top, $bottom)
    {
        $this->mpdf->AddPageByArray([
            'margin-left' => $left,
            'margin-right' => $right,
            'margin-top' => $top,
            'margin-bottom' => $bottom
        ]);
    }

    /**
     *
     * @return string rendered template output
     */
    private function getInstitution()
    {
        return $this->getInstitutionTemplate($this->institution);
    }

    /**
     *
     * @param string $code
     * @param boolean $short
     * @return string rendered template output
     */
    private function getNumber($code, $short = false)
    {
        if ($short)
            return $this->longNumHeader . $code . '.' . $this->date;
        else
            return $this->shortNumHeader . $code . '.' . $this->date; /* change to countered number */
    }

    /* get templates */
    /**
     *
     * @return string rendered template output
     */
    private function getImagesTopTemplate()
    {
        $this->smarty->assign('imgLeftTop', $this->imgLeftTop);
        $this->smarty->assign('imgRightTop', $this->imgRightTop);
        $this->smarty->assign('imgDir', $this->folderChapterPaths['imgPath']);
        return $this->smarty->fetch('dsimagestop.tpl');
    }

    /**
     *
     * @return string rendered template output
     */
    private function getImagesBottomTemplate()
    {
        $this->smarty->assign('imgRightBottom', $this->imgRightBottom);
        $this->smarty->assign('imgDir', $this->folderChapterPaths['imgPath']);
        return $this->smarty->fetch('dsimagesbottom.tpl');
    }

    /**
     *
     * @param string $content
     * @return string rendered template output
     */
    private function getFooterTemplate($content = 'Footer')
    {
        $this->smarty->assign('nbpg', '{nbpg}');
        $this->smarty->assign('PAGENO', '{PAGENO}');
        $this->smarty->assign('pdfPages', $this->pdfPages);
        $this->smarty->assign('pageOf', $this->pageOf);
        $this->smarty->assign('date', $this->date);
        $this->smarty->assign('number', $this->docNumShort);
        $this->smarty->assign('barCode', $this->barCode);
        return $this->smarty->fetch('dsfooter.tpl');
    }

    /**
     *
     * @param string $content
     * @return string rendered template output
     */
    private function getHeaderTemplate($content = 'Header')
    {
        $this->smarty->assign('headerContent', $content);
        return $this->smarty->fetch('dsheader.tpl');
    }

    /**
     *
     * @param string $dataM
     * @param string $dataStrings
     * @return string rendered template output
     */
    private function getDataTemplate($dataM, $dataStrings)
    {
        $this->smarty->assign('dataM', $dataM);
        $this->smarty->assign('dataStrings', $dataStrings);
        return $this->smarty->fetch('dsdata.tpl');
    }

    /**
     *
     * @param string $institution
     * @return string rendered template output
     */
    private function getInstitutionTemplate($institution)
    {
        $this->smarty->assign('institution', $institution);
        return $this->smarty->fetch('dsinstitution.tpl');
    }

    /**
     *
     * @param string $application
     * @return string rendered template output
     */
    private function getApplicationTemplate($application = 'Application')
    {
        $this->smarty->assign('application', $application);
        return $this->smarty->fetch('dsapplication.tpl');
    }

    /**
     *
     * @param string $firstInfo
     * @return string rendered template output
     */
    private function getFirstInfoTemplate($firstInfo = 'First Information')
    {
        $this->smarty->assign('firstInfo', $firstInfo);
        return $this->smarty->fetch('dsfirst-info.tpl');
    }

    /**
     *
     * @param array $refers
     * @return string rendered template output
     */
    private function getEmailDomainTemplate($emailDomain)
    {
        $this->smarty->assign('email', $emailDomain['uName']);
        $this->smarty->assign('domain', '@' . $emailDomain['domain']);
        return $this->smarty->fetch('dsemail.tpl');
    }

    /**
     *
     * @param string $refers
     * @return string rendered template output
     */
    private function getRefersToTemplate($refers)
    {
        $this->smarty->assign('refersTo', $refers);
        return $this->smarty->fetch('dsrefersto.tpl');
    }

    /**
     *
     * @param string $content
     * @return string rendered template output
     */
    private function getContentToTemplate($content)
    {
        $this->smarty->assign('content', $content);
        return $this->smarty->fetch('dscontenttoinstitution.tpl');
    }

    /**
     *
     * @param string $signature
     * @param string $signatureEnd
     * @return string rendered template output
     */
    private function getSignatureTemplate($signature, $signatureEnd)
    {
        $this->smarty->assign('signature', $signature);
        $this->smarty->assign('signatureEnd', $signatureEnd);
        return $this->smarty->fetch('dssignature.tpl');
    }

    /**
     *
     * @return string rendered template output
     */
    private function getDocNumShortTemplate()
    {
        $this->smarty->assign('docNumShort', $this->docNumShort);
        return $this->smarty->fetch('dsdocnumber.tpl');
    }
}
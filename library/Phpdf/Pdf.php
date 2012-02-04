<?php
class Phpdf_Pdf extends Zend_Pdf
{
    protected $_font;
    protected $_imgPath;

    public function __construct($source = null, $revision = null, $load = false)
    {
        parent::__construct($source,$revision,$load);

        $this->_imgPath = realpath(APPLICATION_PATH.'/../public/imgs');
        $this->_font    = realpath(APPLICATION_PATH.'/../public/Times_New_Roman.ttf');
    }

    public function emitirCertificado($participante, $atividade, $qtdHoras)
    {
        $nomeArquivo = 'certificado' . uniqid() . '.pdf';
        $pathArquivo = realpath(APPLICATION_PATH . '/../public/certificado/') . '/' . $nomeArquivo;
        $page        = $this->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
        $resImagem   = imagecreatefromjpeg($this->_imgPath.'/bg_certificado.jpg');
        $primeiraLinha = 'Certificamos que '.$participante;
        $segundaLinha  = 'participou da atividade ' . $atividade;
        $terceiraLinha = '(FLISOL-DF), realizado pela Faculdade Jesus';
        $quartaLinha   = 'Maria José - FAJESU, no dia 24 de abril de 2010,';
        $quintaLinha   = 'com carga horária de '.$qtdHoras.' horas';

        imagettftext($resImagem, 24, 0, 220, 350, -1, $this->_font, $primeiraLinha);
        imagettftext($resImagem, 24, 0, 180, 385, -1, $this->_font, $segundaLinha);
        imagettftext($resImagem, 24, 0, 180, 420, -1, $this->_font, $terceiraLinha);
        imagettftext($resImagem, 24, 0, 180, 455, -1, $this->_font, $quartaLinha);
        imagettftext($resImagem, 24, 0, 180, 490, -1, $this->_font, $quintaLinha);
        imagejpeg($resImagem, $pathArquivo);
        imagedestroy($resImagem);

        $image = new Zend_Pdf_Resource_Image_Jpeg($pathArquivo);

        $page->drawImage($image, 0, 0, 850, 650);
        $this->pages[]    = $page;

        header('Content-Disposition: inline; filename=' . $nomeArquivo);
        header('Content-type: application/x-pdf');
        echo $this->render();
        unlink($pathArquivo);
    }

    public function emitirCertificadoColaborador($participante)
    {
        $nomeArquivo = 'certificado' . uniqid() . '.pdf';
        $pathArquivo = realpath(APPLICATION_PATH . '/../public/certificado/') . '/' . $nomeArquivo;
        $page        = $this->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
        $resImagem   = imagecreatefromjpeg($this->_imgPath.'/bg_certificado.jpg');
        $primeiraLinha = 'Certificamos que '.$participante;
        $segundaLinha  = 'colaborou na realização das atividades no FLISOL-DF 2010';
        $terceiraLinha = 'realizado pela Faculdade Jesus Maria José - FAJESU,';
        $quartaLinha   = 'no dia 24 de abril de 2010, com carga horária de 20 horas';

        imagettftext($resImagem, 24, 0, 220, 350, -1, $this->_font, $primeiraLinha);
        imagettftext($resImagem, 24, 0, 180, 385, -1, $this->_font, $segundaLinha);
        imagettftext($resImagem, 24, 0, 180, 420, -1, $this->_font, $terceiraLinha);
        imagettftext($resImagem, 24, 0, 180, 455, -1, $this->_font, $quartaLinha);
        //imagettftext($resImagem, 24, 0, 180, 490, -1, $this->_font, $quintaLinha);
        imagejpeg($resImagem, $pathArquivo);
        imagedestroy($resImagem);

        $image = new Zend_Pdf_Resource_Image_Jpeg($pathArquivo);

        $page->drawImage($image, 0, 0, 850, 650);
        $this->pages[]    = $page;

        header('Content-Disposition: inline; filename=' . $nomeArquivo);
        header('Content-type: application/x-pdf');
        echo $this->render();
        unlink($pathArquivo);
    }
}

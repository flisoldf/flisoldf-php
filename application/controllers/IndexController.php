<?php
/**
 * @filesource  07/03/2010
 * @author      PHPDF <http://www.phpdf.org.br>
 * @package     <<application>>
 * @subpackage  <<application>>.application.controllers
 * @version     $Id: IndexController.php 149 2010-06-09 17:49:23Z ramon.ornela $
 */
class IndexController extends Phpdf_Controller_Action
{

    public function indexAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('participantes/login');
        }
        $identify = Zend_Auth::getInstance()->getIdentity();
        if ($identify->sRole == Perfil::PALESTRANTE) {
            $this->_redirect('palestrantes');
        }
        $this->_redirect('participantes/participacao-atividades');
    }
}

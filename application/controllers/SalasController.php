<?php
/**
 * @filesource  07/03/2010
 * @author      PHPDF <http://www.phpdf.org.br>
 * @package     <<application>>
 * @subpackage  <<application>>.application.controllers
 * @version     $Id: SalasController.php 150 2010-06-10 13:37:38Z ramon.ornela $
 */
class SalasController extends Phpdf_Controller_Action
{

    public function indexAction()
    {
        $sala = new Sala();
        $rowSetSalas = $sala->fetchAll();
        $this->view->salas = $rowSetSalas;
    }

    public function formularioAction()
    {
        $id = $this->_getParam('id', null);
        $sala = new Sala();
        if ($id) {
            $rowSala = $sala->find($id)->current();
        } else {
            $rowSala = $sala->createRow();
        }

        $this->view->sala = $rowSala;
    }

    public function gravarAction()
    {
        $id = $this->_getParam('id', null);
        $sala = new Sala();

        $dados   = $this->_getAllParams();
        if ($id) {
            $rowSala = $sala->find($id)->current();
        } else {
            $rowSala = $sala->createRow();
            unset($dados['id']);
        }

        $rowSala->setFromArray($dados);
        try {
            $rowSala->save();
        } catch (Exception $e) {
            echo '<pre>Exception: ', print_r($e, true), '</pre>';
            echo '<pre>Data:      ', print_r($rowSala, true), '</pre>';
            exit();
        }

        $this->_redirect('salas/index');
    }
}

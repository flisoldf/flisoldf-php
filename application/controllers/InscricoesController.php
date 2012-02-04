<?php
/**
 * Controle de inscrições
 * @filesource  07/03/2010
 * @author      PHPDF <http://www.phpdf.org.br>
 * @package     <<application>>
 * @subpackage  <<application>>.application.controllers
 * @version     $Id: InscricoesController.php 155 2010-06-14 17:50:23Z ramon.ornela $
 */
class InscricoesController extends Phpdf_Controller_Action
{
    public function indexAction()
    {
    }

    /**
     * Realiza a inscrição do usuário logado
     * @return void
     */
    public function cadastraAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $idUsuario = Zend_Auth::getInstance()->getIdentity()->id;
        $idAtividade = $this->_getParam('id_atividade');

        if (Inscricao::build()->existsByIdUsuarioAndIdAtividade($idUsuario, $idAtividade)) {
            $msg = 'Você já está inscrito nessa atividade';
        } else {
            try {
                $oInscricao = Inscricao::build();
                $vagasOcupadas = $oInscricao->countByAtividade($idAtividade);
                $vagasOcupadas = $vagasOcupadas[$idAtividade];
                $qtVagasSala   = Atividade::build()->find($idAtividade)->current()->findParentSala()->qt_pessoas;
                if($vagasOcupadas >= $qtVagasSala) {
                    $msg = 'Não existe mais vaga para essa atividade';
                } else {
                    $inscricao = $oInscricao->createRow();
                    $inscricao->id_atividade = $idAtividade;
                    $inscricao->id_usuario   = $idUsuario;
                    $inscricao->dt_cadastro  = date('Y-m-d H:i:s');
                    $inscricao->presenca     = 0;
                    $inscricao->save();
                    $msg = 'Inscrição realizada com sucesso';
                }
            } catch (Exception $e) {
                $msg = 'Erro na inscrição';
            }
        }
        $this->getResponse()->setBody($msg);
    }

    public function inscreverAction()
    {
        if ($this->getRequest()->isPost()) {
            $inscricao    = new Inscricao();
            $dados        = $this->_getAllParams();
            $rowInscricao = $inscricao->createRow();
            unset($dados['id']);

            $rowInscricao->setFromArray($dados);
            try {
                $rowInscricao->save();
                $this->_addMessage('Sua inscrição foi realizado com sucesso');
            } catch (Exception $e) {
                $this->_addMessage('Sua inscrição não foi realizado');
            }

        }
        $this->_redirect('index');
    }

    public function cancelarAction()
    {
        if ($this->getRequest()->isGet()) {
             $this->view->nomeUsuario    = Zend_Auth::getInstance()->getIdentity()->nome;
             $this->view->nomeAtividade  = Atividade::build()->find($this->getRequest()->getParam('atividade_id'))->current()->nome;
             $this->view->atividade_id   = $this->getRequest()->getParam('atividade_id');
        }

        if ($this->getRequest()->isPost()) {
            try {
                $idUsuario        = Zend_Auth::getInstance()->getIdentity()->id;
                $idAtividade    = $this->getRequest()->getParam('atividade_id');
                Inscricao::build()->cancelarInscricaoAtividadeUsuario($idUsuario,$idAtividade);
                $this->_addMessage('Inscrição na atividade cancelada com sucesso');
                $this->_redirect('participantes');
            } catch(Exception $e) {
                $this->_addMessage('Não foi possível cancelar a inscrição na atividade');
            }
        }
    }
}

<?php
/**
 * Controle de Atividade
 * @filesource  07/03/2010
 * @author      PHPDF <http://www.phpdf.org.br>
 * @package     <<application>>
 * @subpackage  <<application>>.application.controllers
 * @version     $Id: AtividadesController.php 150 2010-06-10 13:37:38Z ramon.ornela $
 */
class AtividadesController extends Phpdf_Controller_Action
{

    public function indexAction()
    {
        $this->view->atividades      = Atividade::build()->findAll();
        $this->view->countAtividades = Inscricao::build()->countByAtividade();
    }

    public function formularioAction()
    {
        $id = $this->_getParam('id', null);
        $atividade = new Atividade();
        if ($id) {
            $rowAtividade = $atividade->find($id)->current();
            $nomeUsuario  = $rowAtividade->findParentUsuario()->nome;

            // Verifica se o usuário que é palestrante da atividade é o usuário logado
            if (Zend_Auth::getInstance()->getIdentity()->id != $rowAtividade->id_palestrante) {
                if (Zend_Auth::getInstance()->getIdentity()->sRole != Perfil::ADMIN) {
                    $this->_addMessage('Você não pode modificar esta atividade');
                    $this->_redirect('participantes');
                }
            }
        } else {
            $rowAtividade = $atividade->createRow();
            $nomeUsuario  = Zend_Auth::getInstance()->getIdentity()->nome;
        }

        if (Zend_Auth::getInstance()->getIdentity()->sRole == Perfil::ADMIN) {
            // Carrega salas
            $rowSetSalas = Sala::build()->fetchAll(null, 'nome');
            $aSalas = array();
            foreach ($rowSetSalas as $rowSala) {
                $aSalas[$rowSala->id] = $rowSala->nome;
            }
            $this->view->salas               = $aSalas;
            // Carrega situações
            $this->view->situacao = array(
                Atividade::SUBMETIDO => 'Aguardando aprovação',
                Atividade::APROVADO  => 'Aprovado',
                Atividade::REJEITADO => 'Rejeitado'
            );
        }

        $this->view->nomeUsuario = $nomeUsuario;
        $this->view->row         = $rowAtividade;
    }

    public function gravarAction()
    {
        $id = $this->_getParam('id', null);
        $atividade = new Atividade();

        $dados   = $this->_getAllParams();
        if ($id) {
            unset($dados['id_palestrante']);
            unset($dados['dt_cadastro']);
            $rowAtividade = $atividade->find($id)->current();

            // Verifica se o usuário que é palestrante da atividade é o usuário logado
            if(Zend_Auth::getInstance()->getIdentity()->id != $rowAtividade->id_palestrante) {
                if(Zend_Auth::getInstance()->getIdentity()->sRole != Perfil::ADMIN) {
                    $this->_addMessage('Você não pode modificar esta atividade');
                    $this->_redirect('participantes');
                }
            }
        } else {
            $rowAtividade = $atividade->createRow();
            unset($dados['id']);
            $idUsuario = Zend_Auth::getInstance()->getIdentity()->id;
            $dados['id_palestrante'] = $idUsuario;
            $dados['dt_cadastro']    = date('Y-m-d H:i:s');
            $dados['situacao']       = Atividade::SUBMETIDO;
        }

        $rowAtividade->setFromArray($dados);
        try {
            $rowAtividade->save();
            $this->_addMessage('A atividade foi gravada com sucesso');
        } catch (Exception $e) {
            $this->_addMessage('Houve problema na gravação da atividade');
        }
        $this->_redirect('participantes');
    }

    public function visualizarAction()
    {
        $id = $this->_getParam('id', false);
        if ($id !== false) {
            $atividade                           = new Atividade();
            $rowAtividade                        = $atividade->find($id)->current();
            $this->view->id                        = $id;
            $this->view->sala                    = $rowAtividade->findParentSala();
            $this->view->palestrante              = $rowAtividade->findParentUsuario()->toArray();
            $this->view->row                     = $rowAtividade;
            $this->view->participantes            = $rowAtividade->findManyToManyRowset('Usuario','Inscricao');
            $this->view->participantePresente    = $atividade->retornaParticipantesPresentes($id);
        } else {
            $this->_addMessage('É preciso selecionar uma atividade para visualizar');
            $this->_redirect('atividades');
        }
    }

    public function imprimirAction()
    {
        $this->_helper->layout()->disableLayout();
        $idAtividade    = $this->_request->getParam('id', false);
        if ($idAtividade !== false) {
            $this->visualizarAction();
            $this->renderScript('participantes/listagem-participante-impressao.phtml');
        } else {
            $this->_addMessage('É preciso selecionar uma atividade para visualizar');
        }

    }
}

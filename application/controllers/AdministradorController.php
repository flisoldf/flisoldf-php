<?php
/**
 * @filesource  07/03/2010
 * @author      PHPDF <http://www.phpdf.org.br>
 * @package     <<application>>
 * @subpackage  <<application>>.application.controllers
 * @version     $Id: AdministradorController.php 152 2010-06-10 21:14:32Z cristianoteles $
 */
class AdministradorController extends Phpdf_Controller_Action
{
    /**
     * Painel do administrador
     * @return void
     */
    public function indexAction()
    {
        $idUsuario = Zend_Auth::getInstance()->getIdentity()->id;

        // Atividades submetidas
        $this->view->atividadesSubmetidas = Atividade::build()->fetchAll('situacao = ' . Atividade::SUBMETIDO, 'nome');

        // Atividades
        $this->view->atividades = Atividade::build()->findAll();

        // Atividades rejeitadas
        $this->view->atividadesRejeitadas = Atividade::build()->fetchAll('situacao = ' . Atividade::REJEITADO, 'nome');

        // Usuários cadastrados
        $this->view->qtUsuarios   = Usuario::build()->fetchAll()->count();

        // Usuários cadastrados
        $this->view->qtInscricoes = Inscricao::build()->fetchAll()->count();

        // Vagas ocupadas/inscrições
        $this->view->vagasOcupadas = Inscricao::build()->countByAtividade();

        //Atividades que o admin está inscritos
        $this->view->atividadesInscrito = Atividade::build()->findByParticipante($idUsuario);
    }

    public function listagemAction()
    {
        if($this->_getParam('tipo') == Perfil::PARTICIPANTE) {
            $this->view->usuarios = Usuario::build()->fetchAll(null, 'nome');
            $this->render('listagem-usuario');
        } else {
            $this->view->usuarios = Usuario::build()->findPalestrante();
        }
    }

    public function marcarPresencaAction()
    {
        $this->_isAjax();
        $arrParticipante    = $this->_request->getParam('usuario',false);
        $idAtividade        = $this->_request->getParam('id_atividade',false);
        Inscricao::build()->getDefaultAdapter()->beginTransaction();
        try {
            Inscricao::build()->update(array('presenca' =>'N'),'id_atividade='. $idAtividade);
            foreach($arrParticipante as $idUsuario) {
                Inscricao::build()->marcarPresenca($idAtividade,$idUsuario);
            }
            Inscricao::build()->getDefaultAdapter()->commit();
            echo 'Presença marcada com sucesso';
        } catch (Exception $e) {
            Inscricao::build()->getDefaultAdapter()->rollBack();
            echo $e->getMessage();
        }
    }

    public function emailAction()
    {
        if ($this->getRequest()->isPost()) {
            Zend_Debug::dump($this->_request->getParams());
        }
    }

    public function gravarColaboradorAction()
    {
        $colaboradores = (array) $this->_getParam('colaborador');
        if ($colaboradores) {
            $where = 'id IN ('. implode(', ', $colaboradores) . ')';

            $tbUsuario = new Usuario();
            $tbUsuario->update(array('colaborador' => 'null'));
            $tbUsuario->update(array('colaborador' => Usuario::COLABORADOR), $where);

            $this->_addMessage('Colaboradores gravados com sucesso');
        }
        $this->_redirect('administrador/listagem/tipo/' . Perfil::PARTICIPANTE);
    }
}

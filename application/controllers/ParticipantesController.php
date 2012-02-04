<?php
/**
 * Controle de participantes
 * @filesource  07/03/2010
 * @author      PHPDF <http://www.phpdf.org.br>
 * @package     <<application>>
 * @subpackage  <<application>>.application.controllers
 * @version     $Id: ParticipantesController.php 163 2010-06-18 14:07:07Z ramon.ornela $
 */
class ParticipantesController extends Phpdf_Controller_Action
{
    /**
     * Painel do participante
     * @return void
     */
    public function indexAction()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
           $this->_redirect('participantes/login');
        } elseif(Zend_Auth::getInstance()->getIdentity()->sRole == Perfil::ADMIN) {
           $this->_redirect('administrador');
        }

        $idUsuario = Zend_Auth::getInstance()->getIdentity()->id;
        $usuario = Usuario::build()->find($idUsuario)->current();

        // Atividades submetidas
        $this->view->atividadesSubmetidas = Atividade::build()->findByPalestrante($idUsuario);

        // Atividades que esta inscrito
        $this->view->atividadesInscrito = Atividade::build()->findByParticipante($idUsuario);

        // Vagas ocupadas/inscrições
        $this->view->vagasOcupadas = Inscricao::build()->countByAtividade();
    }

    public function editarAction()
    {
        $id = $this->_getParam('id', null);
        $participantes = new Usuario();
        if ($id) {
            $rowParticipante = $participantes->find($id)->current();
        } else {
            $rowParticipantes = $participantes->createRow();
        }
        $this->view->participantes = $rowParticipantes;
    }

    /**
     * Cadastro do participante/usuário
     * @return void
     */
    public function cadastrarAction()
    {
        if ($this->getRequest()->isPost()) {
            $usuario      = new Usuario();
            $dados        = $this->_getAllParams();

            // Verifica se ja existe o email cadastrado
            if ($usuario->hasEmail($dados['email'])) {
                $this->_addMessage('Este email já esta cadastrado na nossa base de dados');
                $this->_redirect('participantes/cadastrar');
                return;
            }

            $rowUsuario = $usuario->createRow();
            unset($dados['id']);

            // Usada para o login
            $senhaLimpa           = $dados['senha'];
            $dados['perfil_id']   = 10;
            $dados['dt_cadastro'] = date('Y-m-d h:i:s');
            $dados['senha']       = Usuario::gerarSenha($dados['senha']);
            $rowUsuario->setFromArray($dados);
            try {
                $rowUsuario->save();

                $this->_addMessage('Você foi cadastrado com sucesso');

                $this->_addMessage('Você foi cadastrado com sucesso, seja bem vindo(a)');

                //envia o e-mail de confirmação de inscrição para o participante
                /*
                $mail    = new Zend_Mail();
                $mail->addTo($dados['email'],$dados['nome']);
                $mail->addTo('arthur.almeidapereira@gmail.com');
                $mail->setSubject('Confirmação Cadastro FLISOL 2010');
                $this->view->nomeParticipanteEmail    = utf8_decode($dados['nome']);
                $mail->setBodyHtml($this->view->render('inscricoes/email_cadastro.phtml'));
                $mail->send();*/

                $usuario = new Usuario();
                if ($usuario->login($dados['email'], $senhaLimpa)) {
                    $this->_redirect('atividades');
                }


            } catch (Exception $e) {
                $this->_addMessage('Seu cadastro não foi realizado');
            }
            $this->_redirect('participantes/cadastrar');
        } else {
            $uf  = new Uf();
            $rowSetUfs = $uf->fetchAll(null, 'nome');

            $ufs = array();
            foreach ($rowSetUfs as $rowUf) {
                $ufs[$rowUf->id] = $rowUf->nome;
            }

            $this->view->ufs = $ufs;
        }
    }

    /**
     * Login dos usuário
     * @return void
     */
    public function loginAction()
    {
        if ($this->_request->isPost()) {
            $usuario = new Usuario();
            if($usuario->login($this->_request->getPost('email'), $this->_request->getPost('senha'))) {
                $this->_redirect('index');
            } else {
                $this->_addMessage('Usuário/email ou senha inválido(s)');
                $this->_redirect('participantes/login');
            }
        }
    }

    /**
     * Logout do usuário
     * @return void
     */
    public function logoutAction()
    {
        $usuario = new Usuario();
        $usuario->logout();
        $this->_redirect('index');
    }

    /**
     * Recupera a senha do usuário
     * @return void
     */
    public function recuperarSenhaAction()
    {
        if ($this->_request->isPost()) {
            $tbUsuario  = new Usuario();
            $rowUsuario = $tbUsuario->fetchRow(array('email = ?' => $this->_getParam('email')));
            if (null === $rowUsuario) {
                $this->_addMessage('E-mail inexistente');
                $this->_redirect('participantes/recuperar-senha');
            }
            $mail = new Zend_Mail('utf-8');
            $mail->addTo($rowUsuario['email'], $rowUsuario['nome'])
                 ->setSubject('[Flisol] Mudar senha')
                 ->setBodyHtml($this->view->partial('email/recuperar-senha.phtml', array('usuario' => $rowUsuario)))
                 ->send();

           $this->_addMessage("Por favor verifique seu e-mail: {$rowUsuario['email']}");
           $this->_redirect('/participantes/recuperar-senha');
        }
    }

    public function mudarSenhaAction()
    {
        $hash  = trim($this->_getParam('hash'));

        if (!$hash) {
            $this->_addMessage('Hash inválido');
            $this->_redirect('/');
        }

        if ($this->_request->isPost()) {
            $senha = $this->_getParam('senha');
            if ($senha !== $this->_getParam('senha-confirmacao')) {
                $this->_addMessage('Senha inválida');
                $this->_redirect("/participantes/mudar-senha/hash/{$hash}");
            }
            list($id, $email) = explode('+', base64_decode($hash));
            $tbUsuario  = new Usuario();
            $rowUsuario = $tbUsuario->fetchRow(
                array(
                    'id    = ?' => $id,
                    'email = ?' => $email
                )
            );

            if (null === $rowUsuario) {
                $this->_addMessage('Usuário inexistente');
                $this->_redirect('/');
            }
            $rowUsuario->senha = Usuario::gerarSenha($senha);
            try {
                $rowUsuario->save();
            } catch (Exception $e) {
                $this->_addMessage('Não foi possível mudar sua senha!');
                $this->_redirect("/participantes/mudar-senha/hash/{$hash}");
            }
            $this->_addMessage('Mudança de senha efetuada com sucesso!');
            $this->_redirect('/');
        }

        $this->view->hash = $hash;
    }

    public function participacaoAtividadesAction()
    {
        $idUsuario  = Zend_Auth::getInstance()->getIdentity()->id;
        $tbUsuario  = new Usuario();
        $usuario    = $tbUsuario->find($idUsuario)->current();
        $select     = Atividade::build()->select()->where("presenca = 'S'");
        $atividades = $usuario->findManyToManyRowset('Atividade', 'Inscricao', 'Usuario', 'Atividade', $select);
        $this->view->colaborador = $usuario->colaborador;
        $this->view->atividades  = $atividades;
    }

    /**
     * Imprimir certificado
     * @return void
     */
    public function certificadoAction()
    {
        $idAtividade = $this->_getParam('id_atividade');
        $this->_helper->layout()->disableLayout();
        $idUsuario  = Zend_Auth::getInstance()->getIdentity()->id;
        $tbUsuario  = new Usuario();
        $usuario    = $tbUsuario->find($idUsuario)->current();
        $select     = Atividade::build()->select()->where("presenca = 'S'");
        $select     = $select->where('id_atividade = ?', $idAtividade);
        $atividades = $usuario->findManyToManyRowset('Atividade', 'Inscricao', 'Usuario', 'Atividade', $select);
        if (!$atividades) {
            $this->_addMessage('Esta atividade não esta com presença para o participante logado');
            $this->_redirect('participantes/participacao-atividades');
        }

        if ($this->_getParam('tipo') === 'pdf') {
            $pdf   = new Phpdf_Pdf();
            $horas = substr($atividades->current()->qt_horas, 0, 5);
            $pdf->emitirCertificado(Zend_Auth::getInstance()->getIdentity()->nome,$atividades->current()->nome,$horas);
        } else {
            $this->view->participante = Zend_Auth::getInstance()->getIdentity();
            $this->view->atividade    = $atividades->current();
        }
    }

    public function certificadoColaboradorAction()
    {
        if (Zend_Auth::getInstance()->getIdentity()->colaborador != Usuario::COLABORADOR) {
            $this->_addMessage('Você não esta definido como colaborador, por favor, procure o administrador');
            //$this->_redirect('participantes/participacao-atividades');
        }
        $this->_helper->layout()->disableLayout();
        if ($this->_getParam('tipo') === 'pdf') {
            $pdf   = new Phpdf_Pdf();
            $pdf->emitirCertificadoColaborador(Zend_Auth::getInstance()->getIdentity()->nome);
        } else {
            $this->view->participante = Zend_Auth::getInstance()->getIdentity();
        }
    }
}

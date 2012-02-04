<?php

class Phpdf_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $oAuth = Zend_Auth::getInstance();
        $oAcl = $this->getAcl();

        // Default role
        $sRole = 'all';
        if ($oAuth->hasIdentity()) {
            $oIdentity = $oAuth->getIdentity();
            $sRole     = isset($oIdentity->sRole) ? $oIdentity->sRole : 'identify';
        }

        $sModule     = $request->module;
        $sController = $request->controller;
        $sAction     = $request->action;
        $sResource   = $sController . ':' . $sAction;

        if($oAcl->has($sResource)) {
            if(!$oAcl->isAllowed('all', $sResource)) {
                // Access is not allowed
                if (!$oAcl->isAllowed($sRole, $sResource)) {
                    //$flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
                    //$flashMessenger->addMessage('Acesso negado');
                    $request->setModuleName('default');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                }
            }
        } else {
            $flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
            $flashMessenger->addMessage('Acesso negado');
            $request->setModuleName('default');
            $request->setControllerName('index');
            $request->setActionName('index');
        }
    }

    protected function getAcl() {
        $oAcl  = new Zend_Acl();

        // Perfis
        $oAcl->addRole(new Zend_Acl_Role('all'));
        $oAcl->addRole(new Zend_Acl_Role('participante'), array('all'));
        $oAcl->addRole(new Zend_Acl_Role('palestrante'), array('participante'));
        $oAcl->addRole(new Zend_Acl_Role('admin'), array('palestrante'));

        // Controllers e Actions
        $oAcl->add(new Zend_Acl_Resource('administrador:index'));
        $oAcl->add(new Zend_Acl_Resource('administrador:listagem'));
        $oAcl->add(new Zend_Acl_Resource('administrador:marcar-presenca'));
        $oAcl->add(new Zend_Acl_Resource('administrador:email'));
        $oAcl->add(new Zend_Acl_Resource('administrador:gravar-colaborador'));

        $oAcl->add(new Zend_Acl_Resource('salas:index'));
        $oAcl->add(new Zend_Acl_Resource('salas:formulario'));
        $oAcl->add(new Zend_Acl_Resource('salas:gravar'));

        $oAcl->add(new Zend_Acl_Resource('index:index'));
        $oAcl->add(new Zend_Acl_Resource('atividades:index'));
        $oAcl->add(new Zend_Acl_Resource('atividades:formulario'));
        $oAcl->add(new Zend_Acl_Resource('atividades:gravar'));
        $oAcl->add(new Zend_Acl_Resource('atividades:visualizar'));
        $oAcl->add(new Zend_Acl_Resource('atividades:imprimir'));

        $oAcl->add(new Zend_Acl_Resource('participantes:index'));
        $oAcl->add(new Zend_Acl_Resource('participantes:cadastrar'));
        $oAcl->add(new Zend_Acl_Resource('participantes:login'));
        $oAcl->add(new Zend_Acl_Resource('participantes:logout'));
        $oAcl->add(new Zend_Acl_Resource('participantes:recuperar-senha'));
        $oAcl->add(new Zend_Acl_Resource('participantes:certificado'));
        $oAcl->add(new Zend_Acl_Resource('participantes:certificado-colaborador'));
        $oAcl->add(new Zend_Acl_Resource('participantes:participacao-atividades'));
        $oAcl->add(new Zend_Acl_Resource('participantes:mudar-senha'));

        $oAcl->add(new Zend_Acl_Resource('inscricoes:index'));
        $oAcl->add(new Zend_Acl_Resource('inscricoes:inscrever'));
        $oAcl->add(new Zend_Acl_Resource('inscricoes:cadastra'));
        $oAcl->add(new Zend_Acl_Resource('inscricoes:cancelar'));

        // Permissões
        $oAcl->allow('all', 'index:index');
        $oAcl->allow('all', 'atividades:index');
        $oAcl->allow('all', 'atividades:visualizar');
        $oAcl->allow('all', 'participantes:cadastrar');
        $oAcl->allow('all', 'participantes:login');
        $oAcl->allow('all', 'participantes:logout');
        $oAcl->allow('all', 'participantes:recuperar-senha');
        $oAcl->allow('all', 'inscricoes:index');
        $oAcl->allow('all', 'inscricoes:cadastra');
        $oAcl->allow('all', 'inscricoes:cancelar');
        $oAcl->allow('all', 'participantes:mudar-senha');

        // Permissões participante
        $oAcl->allow('participante', 'participantes:index');
        $oAcl->allow('participante', 'atividades:formulario');
        $oAcl->allow('participante', 'atividades:gravar');
        $oAcl->allow('participante', 'participantes:certificado');
        $oAcl->allow('participante', 'participantes:certificado-colaborador');
        $oAcl->allow('participante', 'participantes:participacao-atividades');

        // Permissões admin
        $oAcl->allow('admin', 'administrador:index');
        $oAcl->allow('admin', 'administrador:listagem');
        $oAcl->allow('admin', 'salas:index');
        $oAcl->allow('admin', 'salas:formulario');
        $oAcl->allow('admin', 'salas:gravar');
        $oAcl->allow('admin', 'atividades:imprimir');
        $oAcl->allow('admin', 'administrador:marcar-presenca');
        $oAcl->allow('admin', 'administrador:email');
        $oAcl->allow('admin', 'administrador:gravar-colaborador');


        return $oAcl;
    }
}

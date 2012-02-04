<?php

class Phpdf_Controller_Action extends Zend_Controller_Action {

    public function init()
    {
        parent::init();

        // Messages
        $this->view->messages  = $this->_helper->flashMessenger->getMessages();
        $this->_helper->flashMessenger->clearMessages();
    }

    /**
     * Set message in flashMessenger
     * @access protected
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    protected function _addMessage($msg)
    {
        return $this->_helper->flashMessenger($msg);
    }

    protected function _isAjax()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    }
}
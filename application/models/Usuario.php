<?php
// Usuario.php
/**
 * Model de Usuario
 * @filesource  07/03/2010
 * @package     <<application>>
 * @subpackage  <<application>>.application.models
 * @version     $Id: Usuario.php 162 2010-06-18 14:05:59Z ramon.ornela $
 */
class Usuario extends Phpdf_Db_Table
{
    const COLABORADOR = 'S';

    protected $_name = 'usuario';
    protected $_dependentTables  = array( 'Atividade', 'Inscricao' );
    protected $_primary = array('id');

    protected $_referenceMap = array(
        'Perfil' => array(
            'columns'       => 'perfil_id',
            'refTableClass' => 'Perfil',
            'refColumns'    => 'id',
        ),
        'Uf' => array(
            'columns'       => 'uf_id',
            'refTableClass' => 'Uf',
            'refColumns'    => 'id',
        )
    );

    /**
     * Verifica se existe o email passado no banco
     * @param string $email
     * @return boolean
     */
    public function hasEmail($email) {
        // Faz o tratamento para que trate os caracteres de escape
        $email = $this->getAdapter()->quote($email, 'string');

        if($this->fetchRow('email = '. $email)) {
            return true;
        }
        return false;
    }

    /**
     * Faz o login caso o email e senha estejam corretos
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function login($email, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table::getDefaultAdapter(),
            'usuario',
            'email',
            'senha',
            'md5(?)'
        );

        $authAdapter->setIdentity($email)
                    ->setCredential($password);
        $auth   = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if($auth->hasIdentity($authAdapter)) {
            $oIdentify        = $authAdapter->getResultRowObject(null, 'senha');
            // @FIXME Gravar registros em cache
            $codigoPerfil = Perfil::build()->find($oIdentify->perfil_id)
                                           ->current()
                                           ->codigo;
            $oIdentify->sRole = $codigoPerfil;
            $auth->getStorage()->write($oIdentify);
            return true;
        }
        return false;
    }

    /**
     *  Retorna os usuários que são palestrantes
     *  @return Zend_Db_Table_RowSet
     */
    public function findPalestrante()
    {
        $select = $this->select();
        $select->from(array('u' => 'usuario'))
               ->setIntegrityCheck(false)
               ->joinInner(array('a' => 'atividade'), 'u.id = a.id_palestrante');
        return $this->fetchAll($select, 'nome');
    }

   /**
     * Faz o logout do usuario
     * @return boolean
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * Retorna a instância deste objeto
     */
    public static function build()
    {
        return new self();
    }

    /**
     * @param string|null $senha
     * @param int $length
     * @return string
     */
    public function gerarSenha($senha = null, $length = 6)
    {
        if (null !== $senha) {
            return md5($senha);
        }
        /**
         * @todo gerar senha randomica
         */
    }

}

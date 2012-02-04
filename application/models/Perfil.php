<?php
// Perfil.php
/**
 * Model de Perfil
 * @filesource  07/03/2010
 * @package     <<application>>
 * @subpackage  <<application>>.application.models
 * @version     $Id: Perfil.php 149 2010-06-09 17:49:23Z ramon.ornela $
 */
class Perfil extends Phpdf_Db_Table
{
    const PARTICIPANTE = 'participante';
    const PALESTRANTE  = 'palestrante';
    const ADMIN        = 'admin';

    protected $_name = 'perfil';
    protected $_dependentTables  = array( 'Usuario' );
    protected $_primary = array('id');

    /**
     * Retorna a instância deste objeto
     */
    public static function build()
    {
        return new self();
    }
}

<?php
// Uf.php
/**
 * Model de Uf
 * @filesource  07/03/2010
 * @package     <<application>>
 * @subpackage  <<application>>.application.models
 * @version     $Id: Uf.php 148 2010-06-09 17:06:14Z ramon.ornela $
 */
class Uf extends Phpdf_Db_Table
{
    protected $_name = 'uf';
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

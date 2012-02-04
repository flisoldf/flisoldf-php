<?php
// Sala.php
/**
 * Model de Sala
 * @filesource  07/03/2010
 * @package     <<application>>
 * @subpackage  <<application>>.application.models
 * @version     $Id: Sala.php 154 2010-06-11 13:28:38Z ramon.ornela $
 */
class Sala extends Phpdf_Db_Table
{
    protected $_name = 'sala';
    protected $_primary = array('id');

    /**
     * Retorna a instância deste objeto
     */
    public static function build()
    {
        return new self();
    }
}

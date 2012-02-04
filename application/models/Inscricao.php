<?php
// Inscricao.php
/**
 * Model de Inscricao
 * @filesource  07/03/2010
 * @package     <<application>>
 * @subpackage  <<application>>.application.models
 * @version     $Id: Inscricao.php 151 2010-06-10 14:48:46Z ramon.ornela $
 */
class Inscricao extends Phpdf_Db_Table
{
    protected $_name = 'inscricao';
    protected $_referenceMap = array(
            'Atividade' => array(
                            'columns'       => 'id_atividade',
                            'refTableClass' => 'Atividade',
                            'refColumns'    => 'id',
            ),
            'Usuario' => array(
                            'columns'       => 'id_usuario',
                            'refTableClass' => 'Usuario',
                            'refColumns'    => 'id',
    ));

    /**
     * Retorna a instância deste objeto
     * @return Inscricao
     */
    public static function build()
    {
        return new self();
    }

    public function existsByIdUsuarioAndIdAtividade($idUsuario,$idAtividade) {
        $res = $this->select()
                ->from('inscricao', 'count(*) as count')
                ->where('id_usuario = ?',$idUsuario)
                ->where('id_atividade = ?',$idAtividade)
                ->query()
                ->fetch();
        return (bool)$res['count'];
    }

    public function countByAtividade($id = null)
    {
        $select = $this->select();
        $select->from($this, array('COUNT(id_atividade) as count', 'id_atividade'))
               ->group('id_atividade');
        if(is_int($id)) {
            $select->where('id_atividade = ?', $id);
        }
        $rowSet = $this->fetchAll($select);

        $retorno = array();
        foreach($rowSet as $row) {
            $retorno[$row['id_atividade']] = $row['count'];
        }
        return $retorno;
    }

    /**
     * Cancela a inscrição de um usuário em uma atividade
     * @author Arthur Cláudio de Almeida Pereira / arthur.almeidapereira@gmail.com
     * @param int $idUsuario - código do usuÃ¡rio logado
     * @param int $idAtividade - código da atividade
     * @return int número de linhas deletadas
     */
    public function cancelarInscricaoAtividadeUsuario($idUsuario,$idAtividade){
        $where    = 'id_atividade = '.$idAtividade.' and id_usuario = '.$idUsuario;
        return $this->delete($where);
    }


    /**
     * Marca a presença de um aluno em uma atividade
     * @param int $idAtividade
     * @param array $arrIdParticipante
     * @return bool
     */
    public function marcarPresenca($idAtividade,$idParticipante){

        if(empty($idAtividade) || !is_numeric($idAtividade)){
            throw new Exception('ID da atividade informado inválido');
        }

        if(empty($idParticipante) || !is_numeric($idParticipante)){
            throw new Exception('ID do participante informado inválido');
        }
        $where    = 'id_atividade = '. $idAtividade .' and id_usuario = '. $idParticipante;

        if((bool)$this->update(array('presenca'    =>    'S'),$where))
        {
            return true;
        }else
        {
            throw new Zend_Db_Table_Exception('Não foi possível marcar a presença');
        }
    }

}

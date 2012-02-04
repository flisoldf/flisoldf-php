<?php
// Atividade.php
/**
 * Model de Atividade
 * @filesource  07/03/2010
 * @author      Estrada Virtual <http://estradavirtual.com.br>
 * @copyright   Copyright <2010> Estrada Virtual
 * @package     <<application>>
 * @subpackage  <<application>>.application.models
 * @version     $Id: Atividade.php 151 2010-06-10 14:48:46Z ramon.ornela $
 */
class Atividade extends Phpdf_Db_Table
{
    const SUBMETIDO = 1;
    const APROVADO  = 2;
    const REJEITADO = 3;

    protected $_name = 'atividade';
    protected $_dependentTables  = array( 'Inscricao' );
    protected $_primary = array('id');

    protected $_referenceMap = array(
            'Palestrante' => array(
                            'columns'       => 'id_palestrante',
                            'refTableClass' => 'Usuario',
                            'refColumns'    => 'id',
            ),
            'Sala' => array(
                            'columns'       => 'id_sala',
                            'refTableClass' => 'Sala',
                            'refColumns'    => 'id',
    ));

    /**
     * Retorna atividades fazendo join com palestrante e sala
     * @return rows
     */
    public function findAll() {
        $select = $this->select()
                ->from(array('a' => 'atividade'), '*')
                ->setIntegrityCheck(false)
                ->join(array('u' => 'usuario'),'a.id_palestrante = u.id','nome as nome_usuario')
                ->join(array('s' => 'sala'), 'a.id_sala = s.id', array('nome_sala' => 'nome', 'qt_vagas' => 'qt_pessoas'))
                ->where('situacao = ?', Atividade::APROVADO)
                ->order('nome');
        return $this->fetchAll($select);
    }

    /**
     * Retorna atividades do palestrante
     * @return rows
     */
    public function findByPalestrante($idPalestrante)
    {
        $select = $this->select()
                ->from(array('a' => 'atividade'), '*')
                ->setIntegrityCheck(false)
                ->joinLeft(array('s' => 'sala'), 'a.id_sala = s.id', array('nome_sala' => 'nome', 'qt_vagas' => 'qt_pessoas'))
                ->where('id_palestrante = ?', $idPalestrante)
                ->order('nome');
        return $this->fetchAll($select);
    }

    /**
     * Retorna atividades relacionados ao participante
     * @return rows
     */
    public function findByParticipante($idParticipante)
    {
        $select = $this->select()
                ->from(array('a' => 'atividade'), '*')
                ->setIntegrityCheck(false)
                ->joinLeft(array('s' => 'sala'), 'a.id_sala = s.id', array('nome_sala' => 'nome', 'qt_vagas' => 'qt_pessoas'))
                ->join(array('i' => 'inscricao'), 'a.id = i.id_atividade', array())
                ->where('i.id_usuario = ?', $idParticipante)
                ->order('nome');
        return $this->fetchAll($select);
    }

    /**
     * Retorna a instância deste objeto
     * @return Atividade
     */
    public static function build()
    {
        return new self();
    }

/**
     * Marca a presença de um aluno em uma atividade
     * @param int $idAtividade
     * @param array $idParticipante
     * @return array $idParticipante - array com os códigos dos participantes que estavam presentes
     */
    public function retornaParticipantesPresentes($idAtividade){

        if(empty($idAtividade) || !is_numeric($idAtividade)){
            throw new Exception('ID da atividade informado inválido');
        }
        $idParticipantes        = array();

        $select    = $this->select()->where(" presenca = 'S' and id_atividade = ?",$idAtividade);

        $participantesPresentes    = $this->find($idAtividade)
                                        ->current()
                                        ->findManyToManyRowset('Usuario','Inscricao','Atividade','Usuario',$select)->toArray();

        foreach($participantesPresentes    as $presentes)
        {
            $idParticipantes[]    = $presentes['id'];
        }

        return $idParticipantes;
    }
}

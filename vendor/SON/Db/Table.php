<?php

namespace SON\Db;

use \App\Init;

/**
 * Class Table
 * 
 * Contém métodos genéricos para realizar as operações do banco de dados.
 * 
 */

abstract class Table
{

    protected $db;
    protected $table;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    
    /**
    * FUNÇÕES SELECT
    */

    /**
     * Método para retornar um array com os resultados de um select
     * 
     * @param string $fields Campos que devem fazer parte da busca. Ex.: id, nome ou então * para todos os campos.
     * @param string $orderField Campo através do qual os dados serão ordenados. Ex.: nome.
     * @param string $ascDesc Forma de ordenação: ASC, ou DESC.
     * @param string $conditions Informar o parâmetro apenas caso a seleção tenha uma condição, como um WHERE ou INNER JOIN, por exemplo. Caso não seja informado, por padrão, será NULL
     */

    public function select($fields, $orderField, $ascDesc, $conditions = null)
    {
        $query = "SELECT $fields FROM {$this->table} ";
        if($conditions){
            $query .= $conditions;
        }        
        $query .= " ORDER BY $orderField $ascDesc";
        
        $stmt = $this->db->prepare($query);        
        $stmt->execute();
        $res = $stmt->fetchAll();
        return $res;
    }

    /**
     * @param string $fieldName Representa o nome do campo identificador da tabela (normalmente, a chave primária)
     * @param int $id Valor do campo (normalmente o ID) para localizar o registro da tabela.     
     */

    public function findId($fieldName, $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $fieldName = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $res = $stmt->fetch();
        return $res;
    }


    /**
    * FUNÇÕES DELETE
    */

    public function delete($fieldName, $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE $fieldName = :id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }


    /**
     * FUNÇÕES INSERT
     *
     * buildInsert: O método privado buildInsert() tem a função de construir uma instrução SQL de INSERT e posteriormente retornar essa instrução em forma de string. Esse método recebe como parâmetro ($arrayDados) uma array contendo o nome dos campos como chave e os dados que serão inseridos como valores da array.
     * Ex de array: $dados = ['nome' => 'Rafael', 'email' => 'rafaelsaltiel@gmail.com'];
     * insert:O método público insert() chama o método privado buildInsert() para montar a instrução SQL de INSERT e repassa o parâmetro ($arrayDados), posteriormente os valores que serão inseridos são carregados com um loop foreach para instrução.
     */

    private function buildInsert(array $arrayDados)
    {

       // Inicializa variáveis
        $sql = "";
        $campos = "";
        $valores = "";
              
       // Loop para montar a instrução com os campos e valores
        foreach ($arrayDados as $chave => $valor) :
            $campos .= $chave . ', ';
            $valores .= '?, ';
        endforeach;
              
       // Retira vírgula do final da string
        $campos = (substr($campos, -2) == ', ') ? trim(substr($campos, 0, (strlen($campos) - 2))) : $campos ;
              
       // Retira vírgula do final da string
        $valores = (substr($valores, -2) == ', ') ? trim(substr($valores, 0, (strlen($valores) - 2))) : $valores ;
              
       // Concatena todas as variáveis e finaliza a instrução
        $sql .= "INSERT INTO {$this->table} (" . $campos . ")VALUES(" . $valores . ")";
              
       // Retorna string com instrução SQL
        return trim($sql);
    }

    public function insert(array $arrayDados)
    {
        $sql = $this->buildInsert($arrayDados);
        $stmt = $this->db->prepare($sql);
        $cont = 1;
        foreach ($arrayDados as $valor) :
            $stmt->bindValue($cont, $valor);
            $cont++;
        endforeach;
        // Executar a query
        return $stmt->execute();
    }

    
    /**
     * Retorna o último ID cadastrado em um INSERT     
     */

    public function lastInsertId()
    {
        $stmt = $this->db->prepare("SELECT LAST_INSERT_ID()");       
        $stmt->execute();
        $res = $stmt->fetch();
        return $res;
    }


    /**
    * FUNÇÕES UPDATE
    *
    * Método privado para construção da instrução SQL de UPDATE
    * @param $arrayDados = Array de dados contendo colunas, operadores e valores
    * @param $arrayCondicao = Array de dados contendo colunas e valores para condição WHERE
    * @return String contendo instrução SQL
    */

    private function buildUpdate($arrayDados, $arrayCondicao)
    {
    
       // Inicializa variáveis
        $sql = "";
        $valCampos = "";
        $valCondicao = "";
              
       // Loop para montar a instrução com os campos e valores
        foreach ($arrayDados as $chave => $valor) :
            $valCampos .= $chave . '=?, ';
        endforeach;
              
       // Loop para montar a condição WHERE
        foreach ($arrayCondicao as $chave => $valor) :
            $valCondicao .= $chave . '? AND ';
        endforeach;
              
       // Retira vírgula do final da string
        $valCampos = (substr($valCampos, -2) == ', ') ? trim(substr($valCampos, 0, (strlen($valCampos) - 2))) : $valCampos ;
              
       // Retira vírgula do final da string
        $valCondicao = (substr($valCondicao, -4) == 'AND ') ? trim(substr($valCondicao, 0, (strlen($valCondicao) - 4))) : $valCondicao ;
              
        // Concatena todas as variáveis e finaliza a instrução
        $sql .= "UPDATE {$this->table} SET " . $valCampos . " WHERE " . $valCondicao;

        // Retorna string com instrução SQL
        return trim($sql);
    }

    /*
    * Método público para atualizar os dados na tabela   
    * @param $arrayDados = Array de dados contendo colunas e valores   
    * @param $arrayCondicao = Array de dados contendo colunas e valores para condição WHERE - Exemplo array('$id='=>1)   
    * @return Retorna resultado booleano da instrução SQL   
    */
    public function update($arrayDados, $arrayCondicao)
    {
        try {
           // Atribui a instrução SQL construida no método
            $sql = $this->buildUpdate($arrayDados, $arrayCondicao);

           // Passa a instrução para o PDO
            $stmt = $this->db->prepare($sql);            
    
           // Loop para passar os dados como parâmetro
            $cont = 1;
            foreach ($arrayDados as $valor) :
                $stmt->bindValue($cont, $valor);
                $cont++;
            endforeach;
              
           // Loop para passar os dados como parâmetro cláusula WHERE
            foreach ($arrayCondicao as $valor) :
                $stmt->bindValue($cont, $valor);
                $cont++;
            endforeach;
    
           // Executa a instrução SQL e captura o retorno
            $retorno = $stmt->execute();
    
            return $retorno;
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
}

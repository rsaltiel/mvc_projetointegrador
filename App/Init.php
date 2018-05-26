<?php
namespace App;

use SON\Init\Bootstrap;

class Init extends Bootstrap
{
    /**
     * Rotas do admin
     * @return array da rota com Controller e Método chamado pela URL
     */
    protected function initRoutes()
    {        

        /**
        * Defina os arrays de rotas de seu sistema logo abaixo deste bloco de comentário (em linhas anteriores ao método setRoutes);
        * Cada declaração contém o caminho(rota), o Controller e o método deste Controller. O método do Controller será o responsável por dizer ao seu sistema o que aquela URL fará.
        * Exs.:
        * 
        * $ar['cliente'] = array('route' => '/cliente', 'controller' => 'cliente', 'action' => 'clienteListar');             * 
        * Neste exemplo, ao acessar o endereço www.dominiodesuaaplicacao.com.br/cliente, a aplicação entenderá que está sendo chamado o controller Cliente e o método clienteListar deste controller. Neste método serão definadas as ações que serão realizadas, ou seja, a listagem de clientes. Utilize, no array, o nome do controller sempre em minúsculas (cliente).
        * 
        * $ar['cliente-cadastrar'] = array('route' => '/cliente-cadastrar', 'controller' => 'cliente', 'action' => 'clienteCadastrar');     
        * Neste exemplo, ao acessar o endereço www.dominiodesuaaplicacao.com.br/cliente-cadastrar, a aplicação entenderá que está sendo chamado o controller Cliente e o método clienteCadastrar deste controller. Neste método serão definadas as ações que serão realizadas, ou seja, o formulário de cadastro de clientes. Utilize, no array, o nome do controller sempre em minúsculas (cliente).
        */         

        // Rota da página inicial da aplicação. Chama o Controller Index, método (action) index() 

        $ar['/'] = array('route' => '/', 'controller' => 'index', 'action' => 'index');
        
        $this->setRoutes($ar);
    }

    /**
     * Instancia o PDO
     * 
     * Você deverá alterar as configurações de seu banco logo abaixo (SGBD, host, usuário e senha). Como todo o código desta aplicação foi estruturado usando o PDO, você poderá usar diferentes bancos facilmente sem precisar fazer alterações no restante da aplicação. Abaixo, o SGBD foi definido como mysql. Caso sua aplicação utilize PostgreSQL, por exemplo, mude de mysql para pgsql
     *  
     * @return $db retorna uma instância da conexão
     */

    public static function getDb()
    {
        switch ($_SERVER['HTTP_HOST']) {
            /* Config da instalação local, através de Virtual Host */
            case 'www.projetointegrador.com.br':
                $host = "localhost";
                $dbname = "nome-do-banco";
                $user = "root";
                $password = "";
                break;
            /* Config do ambiente de produção */
            default:
                $host = "";
                $dbname = "";
                $user = "";
                $password = "";
                break;
        }
        $db = new \PDO("mysql:host=$host;dbname=$dbname", $user, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        return $db;
    }
}

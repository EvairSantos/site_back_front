<?php

// Inclui o arquivo que contém a classe responsável pela conexão com o banco de dados.
require_once '../src/Core/Database.php';

/**
 * Classe Migration
 * 
 * Responsável por executar as migrações de banco de dados a partir de um arquivo SQL.
 */
class Migration {
    private $db;

    /**
     * Construtor da classe Migration.
     *
     * @param PDO $db Instância de conexão com o banco de dados.
     */
    public function __construct($db) {
        $this->db = $db; // Armazena a instância de conexão com o banco de dados.
    }

    /**
     * Executa as migrações.
     *
     * @param string $sqlFile Caminho para o arquivo SQL que contém as instruções de migração.
     */
    public function run($sqlFile) {
        // Lê o conteúdo do arquivo SQL.
        $sql = file_get_contents($sqlFile);
        
        if ($sql === false) {
            // Se houver erro ao ler o arquivo, o script é interrompido.
            die("Erro ao ler o arquivo SQL.");
        }

        try {
            // Executa as instruções SQL no banco de dados.
            $this->db->exec($sql);
            echo "Migração realizada com sucesso!\n";
        } catch (PDOException $e) {
            // Em caso de erro na execução das instruções SQL, exibe a mensagem de erro e interrompe o script.
            die("Erro na migração: " . $e->getMessage());
        }
    }
}

// Cria uma instância da conexão com o banco de dados.
$database = new Database();
$pdo = $database->getConnection(); // Obtém a conexão PDO a partir da classe Database.

// Cria uma instância da classe Migration e executa a migração.
$migration = new Migration($pdo);
$migration->run('database.sql'); // Especifica o arquivo SQL a ser executado.

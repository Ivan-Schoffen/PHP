<?php

namespace App\Dal;

// Importa classes nativas do núcleo do PHP usadas para banco de dados.
// PDO (PHP Data Objects) é a interface robusta do PHP para acesso a banco de dados.
use PDO;
use PDOException;
use Exception;

// A classe 'abstract' funciona como um molde de base. Ela não pode ser instanciada diretamente 
// (não se pode fazer '$c = new Conn()'). Usamos ela apenas para agrupar essa lógica estática.
abstract class Conn{
    // Propriedades estáticas ('static') pertencem à classe toda e não a um objeto criado dela.
    // É o equivalente em C a declarar variáveis globais static no início do arquivo, onde o 
    // mesmo valor na memória é compartilhado com todas as partes do programa que o acessam.
    
    // O modificador '?' antes de PDO diz que essa variável pode ser ou NULL ou do tipo PDO.
    private static ?PDO $conn = null;
    private static string $host = "localhost:3306";
    private static string $dbname = "test"; // Nome do banco de dados
    private static string $user = "root"; // Usuário padrão do XAMPP
    private static string $password = ""; // Senha padrão vazia no XAMPP

    // Método estático para obter a conexão. Isso implementa o padrão "Singleton" (Garantir uma única instância).
    public static function getConn() : PDO {
        // Se o ponteiro/variável estática da conexão for nulo, indica que não abrimos a conexão ainda nesta rodada.
        if (self::$conn === null) {
            // O bloco try/catch captura exceções (erros "fatais").
            // É útil para que o programa não encerre de forma agressiva (Segmentation Fault) e sim controlada.
            try {
                // Instancia um objeto de conexão.
                // 'self::' é usado para acessar variáveis da própria classe, assim como acessaríamos variáveis globais.
                self::$conn = new PDO("mysql:host=". self::$host . ";dbname=" . self::$dbname, self::$user, self::$password);
            } catch (PDOException $e) {
                // Caso ocorra falha de conexão (senha errada, mysql fora), "lançamos" (throw) uma nova Exception customizada.
                throw new Exception("Erro ao conectar ao banco de dados: " . $e->getMessage(), 1);    
            }   
        }
        // Se a conexão não era nula ou se acabou de ser criada, devolve essa mesma conexão para quem chamou.
        return self::$conn;
    }
}
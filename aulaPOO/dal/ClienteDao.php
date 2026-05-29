<?php
namespace App\Dal;

// Importamos a classe de Conexão e o Modelo de Cliente
use App\Dal\Conn;
use App\Model\Cliente;
use Exception;
use PDO;
use PDOException;

// DAO significa Data Access Object. É o padrão usado para centralizar 
// todas as operações de banco de dados (CRUD) referentes a 'Cliente'.
// Se no C faríamos funções como inserir_cliente_bd(), aqui nós as agrupamos na classe ClienteDao.
abstract class ClienteDao{
    
    // Recebe um objeto da classe Cliente (em PHP, objetos são passados por referência implicitamente)
    // Retorna um inteiro (int), que será o ID inserido no banco.
    public static function cadastrar(Cliente $cliente) : int {
        try {
            // Pega a conexão através do Singleton criado no arquivo Conn.php
            $pdo = Conn::getConn();    
            // 'prepare' é equivalente a preparar um statement (declaração) SQL.
            // Protege o sistema contra SQL Injection (ataque de hackers).
            // Em vez de concatenar variáveis na string (como num sprintf perigoso do C), 
            // usamos variáveis temporárias no SQL (ex: :nome).
            $sql = $pdo->prepare("INSERT INTO clientes (nome, sobrenome, ddd, telefone, email) VALUES (:nome, :sobrenome, :ddd, :telefone, :email)");

            // 'bindValue' liga os parâmetros da string SQL temporária com os valores do nosso objeto Cliente.
            // PDO::PARAM_STR e INT dizem ao banco o tipo de dado esperado.
            $sql->bindValue(":nome", $cliente->getNome(), PDO::PARAM_STR);
            $sql->bindValue(":sobrenome", $cliente->getSobrenome(), PDO::PARAM_STR);
            $sql->bindValue(":ddd", $cliente->getDdd(), PDO::PARAM_INT);
            $sql->bindValue(":telefone", $cliente->getTelefone(), PDO::PARAM_INT);
            $sql->bindValue(":email", $cliente->getEmail(), PDO::PARAM_STR);
            
            // Executa a instrução SQL no banco de dados.
            $sql->execute();

            // Retorna o último ID gerado automaticamente pelo auto_increment do MySQL.
            return (int) $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Método para listar todos os clientes do banco de dados. 
    // Retorna um array (matriz) de objetos Cliente.
    public static function listar(): array{
        try{
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM clientes");
            $sql->execute();

            // fetchAll pega todos os registros devolvidos pelo banco e os transforma num array associativo 
            // (um array onde os índices são os nomes das colunas da tabela).
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            // Array vazio para armazenar a lista de objetos
            $clientes = [];

            // foreach é um loop que percorre cada item do array $res (como um while não nulo no C)
            foreach($res as $dados){
                // Para cada linha do banco, nós alimentamos a fábrica do Modelo Cliente
                // e salvamos o novo objeto Cliente na última posição do array $clientes.
                $clientes[] = Cliente::criar(
                    $dados["id"],
                    (int) $dados["ddd"],
                    $dados["telefone"],
                    $dados["nome"],
                    $dados["sobrenome"],
                    $dados["email"]
                );
            }

            return $clientes;

        }catch(PDOException $e){
            throw $e;
        }
    }

    // Busca um cliente específico pelo ID. 
    // O '?' antes de Cliente significa que a função pode retornar um objeto Cliente OU nulo (NULL) se não achar nada.
    public static function buscarPorId(int $id) : ?Cliente {
        try{
            $pdo = Conn::getConn();
            // A interrogação '?' no SQL é outra forma de preparar a query (bind posicional).
            $sql = $pdo->prepare("SELECT * FROM clientes WHERE id=?");
            // O valor que vai substituir a '?' é passado dentro do array no execute.
            $sql->execute([$id]);
            // 'fetch' (sem All) pega apenas UMA linha do resultado, pois ID é único.
            $dados = $sql->fetch(PDO::FETCH_ASSOC);

            // Se o banco não devolveu dados, retorna NULL (como retornar ponteiro NULL em C).
            if (!$dados) return null;

            // Retorna um novo objeto Cliente preenchido com os dados daquela única linha.
            return Cliente::criar(
               $dados["id"], 
               $dados["ddd"], 
               $dados["telefone"], 
               $dados["nome"], 
               $dados["sobrenome"], 
               $dados["email"]
            );
        }catch(PDOException $e){
            throw $e;
        }
    }

    public static function excluir(int $id) : void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("DELETE FROM clientes WHERE id=?");
            $sql->execute([$id]);

            // rowCount conta quantas linhas da tabela foram afetadas pelo DELETE. 
            // Se for diferente de 1, significa que ele não conseguiu apagar.
            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao deletar Cliente");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function editar(Cliente $cliente) : void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("UPDATE clientes SET nome=?, sobrenome=?, ddd=?, telefone=?, email=? WHERE id=?");
            // Passamos os valores num array. Eles vão substituir as '?' na exata mesma ordem que aparecem no SQL acima.
            $sql->execute([
                $cliente->getNome(),
                $cliente->getSobrenome(),
                $cliente->getDdd(),
                $cliente->getTelefone(),
                $cliente->getEmail(),
                $cliente->getId()
            ]);
            
            // Se as linhas alteradas não for 1, lança erro.
            if ($sql->rowCount() !==1) {
                throw new Exception("Nenhum registro foi alterado");
            }

        } catch (PDOException $e) {
            throw new PDOException("Erro ao editar");
        }
    }
}

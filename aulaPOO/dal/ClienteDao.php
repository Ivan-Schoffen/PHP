<?php
namespace App\Dal;

use App\Dal\Conn;
use App\Model\Cliente;
use Exception;
use PDO;
use PDOException;

abstract class ClienteDao{
    public static function cadastrar(Cliente $cliente) : int {
        try {
        $pdo = Conn::getConn();    
        $sql = $pdo->prepare("INSERT INTO clientes (nome, sobrenome, ddd, telefone, email) VALUES (:nome, :sobrenome, :ddd, :telefone, :email)");

    $sql->bindValue(":nome", $cliente->getNome(), PDO::PARAM_STR);
    $sql->bindValue(":sobrenome", $cliente->getSobrenome(), PDO::PARAM_STR);
    $sql->bindValue(":ddd", $cliente->getDdd(), PDO::PARAM_INT);
    $sql->bindValue(":telefone", $cliente->getTelefone(), PDO::PARAM_INT);
    $sql->bindValue(":email", $cliente->getEmail(), PDO::PARAM_STR);
    $sql->execute();

    return (int) $pdo->lastInsertId();
    } catch (PDOException $e) {
        throw $e;
    }
    }

    public static function listar(): array{
        try{
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM clientes");
            $sql->execute();

            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            $clientes = [];

            foreach($res as $dados){
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

    public static function buscarPorId(int $id) : ?Cliente {
        try{
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM clientes WHERE id=?");
            $sql->execute([$id]);
            $dados = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$dados) return null;

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
            $sql->execute([
                $cliente->getNome(),
                $cliente->getSobrenome(),
                $cliente->getDdd(),
                $cliente->getTelefone(),
                $cliente->getEmail(),
                $cliente->getId()
            ]);
            if ($sql->rowCount() !==1) {
                throw new Exception("Nenhum registro foi alterado");
            }

        } catch (PDOException $e) {
            throw new PDOException("Erro ao editar");
        }
    }
}

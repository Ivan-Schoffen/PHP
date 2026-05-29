<?php
namespace App\Controller;

use App\Util\Functions as Util;
use App\Model\Cliente;
use App\Dal\ClienteDao;
use App\View\clienteView;

use \Exception;

class ClienteController{
    public static ?string $msg = null;

    public static function cadastrar() : void {
        if ($_SERVER['REQUEST_METHOD'] ==="POST" && isset($_POST["nome"])) {
            [$nome, $sobrenome, $ddd, $telefone, $email] = array_map([Util::class, "preparaTexto"], array_values($_POST));
            
            try{
                $cliente = Cliente::criar(null, (int)$ddd, (int)$telefone, $nome, $sobrenome, $email);
                /*echo "<pre>";
                var_dump($cliente);
                echo "</pre>";
                exit;*/
                $id = ClienteDao::cadastrar($cliente);

                header("Location: ?p=cad");
                exit;

            } catch (\Exception $e){
                self::$msg = $e->getMessage();
            }
        }

        clienteView::formulario(self::$msg);
    }

    public static function editar(): void {
        $cliente = null;
        if (isset($_GET["alt"])) {
            $cliente = ClienteDao::buscarPorId((int)$_GET["alt"]);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            [$id, $nome, $sobrenome, $ddd, $telefone, $email] = array_map([Util::class, "preparaTexto"], array_values($_POST));

            try {
                $cliente = Cliente::criar((int)$id, $ddd, $telefone, $nome, $sobrenome, $email);
                ClienteDao::editar($cliente);
                header("Location: ?p=list");
                exit;
            } catch (Exception $e) {
                self::$msg = $e->getMessage();
            }
        }

        clienteView::formulario(self::$msg, $cliente);
    }

    public static function listar(?int $deletar = null) : void {
        $ordenar = match($_GET["order"] ?? ""){
            "nome" => "getNome",
            "sobrenome" => "getSobrenome",
            "ddd" => "getDdd",
            "telefone" => "getTelefone",
            "email" => "getEmail",
            default => "getId"
        };

        $clientes = ClienteDao::listar();
        usort($clientes, fn($a, $b) => $a->$ordenar() <=> $b->$ordenar());
        clienteView::listar($clientes, $deletar);
    }

    public static function buscarClientePorId(int $id) : ?Cliente {
        return ClienteDao::buscarPorId($id);
    }

    public static function deletar() : void {
        if (isset($_GET["del"])) {
            self::listar((int)$_GET["del"]);
        }

        if (isset($_GET["deletar"])) {
            ClienteDao::excluir((int)$_GET["deletar"]);
            header("Location: ?p=list");
            exit;
        }
    }
}

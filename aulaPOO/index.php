<?php
declare(strict_types=1);
namespace App;

require_once "./Autoload.php";
use App\Controller\clienteController as Cliente;

?>
<!DOCTYPE HTML>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title> Página com BD</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <header>
        <nav>
            <?php require_once("./menu.php");?>
        </nav>
    </header>
    <main>
    <?php
        $page = $_GET["p"] ?? "home";
        match($page) {
            "home" => require_once("./view/home.php"),
            "list" => Cliente::listar(),
            "cad" => Cliente::cadastrar(),
            "alt" => Cliente::editar(),
            "deletar" => Cliente::deletar(),
            default => require_once("./view/404.php"),
        };
    ?>
    </main>
    <footer>
        <small>
            Copyright &copy; - <?= date("Y") ?>
        </small>
    </footer>
</body>
</html>

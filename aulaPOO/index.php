<?php
// declare(strict_types=1) é como ativar um "modo rigoroso" no C (como a flag -Wall no gcc). 
// Ele força o PHP a verificar os tipos das variáveis rigorosamente (ex: não aceitar int se pediu string).
declare(strict_types=1);

// 'namespace' é como organizar seu código em "pastas virtuais" para evitar conflitos de nomes.
// Como no C não temos isso, costumamos usar prefixos nas funções (ex: db_conectar()).
namespace App;

// 'require_once' é o equivalente ao #include do C. Ele inclui o arquivo na memória, mas garante 
// que seja incluído apenas uma vez para não dar erro de redefinição múltipla.
require_once "./Autoload.php";

// 'use' importa a classe do namespace específico. Assim podemos usar apenas 'Cliente' no código
// ao invés de digitar o caminho completo 'App\Controller\clienteController' toda vez.
use App\Controller\clienteController as Cliente;

?>
<!DOCTYPE HTML>
<!-- Aqui começa o HTML, que é apenas texto de marcação enviado para o navegador -->
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title> Página com BD</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <header>
        <nav>
            <?php 
            // Inclui o arquivo do menu aqui. É como colar o conteúdo de menu.php neste lugar.
            require_once("./menu.php");
            ?>
        </nav>
    </header>
    <main>
    <?php
        // $_GET é uma matriz associativa (array) GLOBAL e pré-definida no PHP.
        // Ela contém as variáveis passadas pela URL do navegador (ex: index.php?p=list).
        // O operador '??' (null coalescing) verifica se $_GET["p"] existe. 
        // Se existir, $page recebe o valor. Senão, recebe a string "home" (um valor padrão seguro).
        $page = $_GET["p"] ?? "home";

        // 'match' é a evolução moderna do 'switch/case' que você conhece do C. 
        // Ele avalia o valor da string $page e executa o lado direito correspondente.
        match($page) {
            "home" => require_once("./view/home.php"), // Se for "home", carrega a página inicial
            "list" => Cliente::listar(), // Chama o método 'listar' da classe Controller (equivalente a uma chamada de função)
            "cad" => Cliente::cadastrar(), // Chama o método 'cadastrar'
            "alt" => Cliente::editar(), // Chama o método 'editar'
            "deletar" => Cliente::deletar(), // Chama o método 'deletar'
            default => require_once("./view/404.php"), // Caso seja uma string desconhecida (default/else)
        };
    ?>
    </main>
    <footer>
        <small>
            <!-- A tag < ? = ... ? > é um atalho para < ? php echo ... ; ?> (imprimir na tela como o printf).
                 A função 'date("Y")' é nativa do PHP e devolve o ano atual. -->
            Copyright &copy; - <?= date("Y") ?>
        </small>
    </footer>
</body>
</html>

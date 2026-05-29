<?php
// O PHP tem uma funcionalidade chamada "Autoload". Em C, temos que dar #include
// em todos os arquivos necessários toda vez. No PHP, podemos ensinar o sistema a 
// dar o include automático ("require") na classe certa apenas no momento em que a usarmos.

// spl_autoload_register registra uma função que será chamada pelo núcleo do PHP 
// sempre que tentarmos usar uma classe que ainda não foi incluída.
spl_autoload_register( function($namespace){
    // '__DIR__' é uma constante mágica que sempre contém o caminho do diretório do arquivo atual.
    // 'DIRECTORY_SEPARATOR' é a barra ( / ou \ ) correta, que muda dependendo se é Windows ou Linux.
    // O que essa linha faz: Substitui a string do namespace ('App\') para calcular o caminho físico do arquivo.
    $arquivo = __DIR__ . DIRECTORY_SEPARATOR 
    . str_replace(['App\\', '\\'], ['', DIRECTORY_SEPARATOR], $namespace) . ".php";

    // file_exists é parecido com tentar abrir o arquivo e ver se o ponteiro não é NULL (em C).
    if (file_exists($arquivo)) {
        // Se o arquivo da classe existe, fazemos o include (#include) dele aqui dinamicamente.
        require_once $arquivo;
    }
});
?>
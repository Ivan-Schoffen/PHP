<?php
namespace App\Util;
// Classe apenas para abrigar funções utilitárias que podem ser chamadas em vários lugares (Helpers).
// É como se você tivesse um arquivo "utils.c" com várias funções globais pra ajudar em operações de string ou matemática.
class Functions{
    // O 'static' permite que chamemos Functions::preparaTexto() sem precisar instanciar a classe.
    static function preparaTexto(string $texto): string {
        // 'trim' recorta os espaços inúteis no começo e no fim da palavra (ex: "  texto  " vira "texto").
        // 'htmlentities' converte símbolos (<, >, &) em entidades HTML seguras (&lt;, &gt;).
        // Isso impede ataques XSS (quando o usuário tenta digitar uma tag <script> no input para hackear o site).
        return trim(htmlentities($texto));
    }
}

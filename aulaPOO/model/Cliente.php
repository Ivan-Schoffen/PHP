<?php
namespace App\Model;

// O Modelo (Model) representa uma entidade de dados. É aqui que moldamos o Cliente.
// Em C, isso é o equivalente direto a definir uma 'struct Cliente { int id; char nome[50]; ... }'.
// A diferença é que a 'classe' (orientação a objetos) permite colocar junto dos dados (variáveis)
// as funções (métodos) que os manipulam.
class Cliente{
    // O 'private' cria o Encapsulamento. Ele blinda essas propriedades para que ninguém
    // de fora da classe consiga acessá-las ou alterá-las diretamente (ex: '$cliente->id = 5' vai dar erro de fora).
    private ?int $id;
    private int $ddd;
    private int $telefone;
    private string $nome;
    private string $sobrenome;
    private string $email;

    // O Construtor é a função chamada assim que um objeto é criado (quando fazemos 'new Cliente()').
    // Ao definirmos ele como 'private', proibimos os programadores de instanciar o objeto livremente com 'new'.
    // É uma forma de obrigar todos a usarem a nossa função de fábrica abaixo (o método 'criar').
    private function __construct(){}

    // Método estático 'fábrica'. É a única porta de entrada para criar um objeto Cliente.
    // Assim como em C poderíamos ter uma função 'Cliente* criar_cliente(...)'.
    public static function criar(?int $id, int $ddd, int $telefone, string $nome, string $sobrenome, string $email) : static {
        // Aqui dentro a classe tem permissão de usar o 'new' nela mesma, alocando memória para o objeto.
        $cliente = new static();
        
        // Como o 'id' geralmente vem do Banco de Dados e não precisa de validação de regras de negócio, recebe direto.
        $cliente->id = $id; 
        
        // Já os outros campos são passados pelos métodos 'set', que contêm "ifs" para validar os dados,
        // garantindo que não entraremos com dados sujos na nossa "struct" blindada.
        $cliente->setNome($nome);
        $cliente->setSobrenome($sobrenome);
        $cliente->setDdd($ddd);
        $cliente->setTelefone($telefone);
        $cliente->setEmail($email);

        // Retorna o objeto Cliente (ponteiro em memória) recém-criado.
        return $cliente;
    }

    // Getters: Funções para que agentes externos possam LER as propriedades protegidas (private).
    public function getId(): ?int { return $this->id; }
    public function getDdd(): int { return $this->ddd; }
    public function getTelefone(): int { return $this->telefone; }
    public function getNome(): string { return $this->nome; }
    public function getSobrenome(): string { return $this->sobrenome; }
    public function getEmail(): string { return $this->email; }

    // Setters: Funções para GRAVAR as propriedades protegidas.
    // É o mecanismo perfeito para aplicar regras de validação nos dados.

    public function setNome(string $nome) : void {
        // Se a string passada for vazia, nós abortamos e "explodimos" um erro.
        if ($nome == null || $nome == "") {
            throw new \InvalidArgumentException("O nome é obrigatório.");
        }
        // '$this' é equivalente ao ponteiro 'this' no C++. É a referência para acessar as variáveis
        // do próprio objeto (da instância sendo usada) que chamou a função.
        $this->nome = $nome;
    }

    public function setSobrenome(string $sobrenome) : void {
        if ($sobrenome == null || $sobrenome == "") {
            throw new \InvalidArgumentException("O sobrenome é obrigatório.");
        }
        $this->sobrenome = $sobrenome;
    }

    public function setDdd(int $ddd): void {
        // Validação de regra: O DDD deve ter obrigatoriamente 2 dígitos.
        if ($ddd < 10 || $ddd > 99 ) {
            throw new \InvalidArgumentException("O DDD precisa ser válido");
        }
        $this->ddd = $ddd;
    }

    public function setTelefone(int $telefone): void {
        // Aqui convertemos o número inteiro ($telefone) em texto (string) apenas para poder 
        // usar a função strlen() e contar a quantidade de dígitos dele. Regra de tamanho do telefone.
        if (strlen((string)$telefone) != 9) {
            throw new \InvalidArgumentException("O telefone precisa ter 9 digitos");
        }
        $this->telefone = $telefone;
    }

    public function setEmail(string $email) : void {
        if($email == null || $email == "") {
            throw new \Exception("O email é obrigatório");
        }
        $this->email = $email;
    }

}

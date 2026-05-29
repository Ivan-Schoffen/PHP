<?php
namespace App\Controller;

use App\Util\Functions as Util;
use App\Model\Cliente;
use App\Dal\ClienteDao;
use App\View\clienteView;

use \Exception;

// O Controller (Controlador) é a ponte (o cérebro) do padrão MVC.
// Ele pega os dados vindos das telas (View), aplica regras ou constrói o Modelo (Model),
// e manda salvar no Banco de Dados (através do DAO). E também faz o caminho inverso.
class ClienteController{
    // Variável estática para guardar mensagens de erro de validação que possam surgir.
    public static ?string $msg = null;

    public static function cadastrar() : void {
        // $_SERVER é uma variável GLOBAL do PHP com dados sobre a conexão atual.
        // 'REQUEST_METHOD' === "POST" checa se o usuário submeteu um formulário usando o método POST.
        // 'isset($_POST["nome"])' verifica se no formulário havia um campo input com o name "nome".
        if ($_SERVER['REQUEST_METHOD'] ==="POST" && isset($_POST["nome"])) {
            // $_POST é outra variável GLOBAL que armazena todos os campos preenchidos do formulário.
            // array_values($_POST) pega os valores digitados,
            // array_map passa cada valor para a função 'preparaTexto' (sanitizando os dados).
            // Do lado esquerdo usamos colchetes [...] para desestruturar a resposta e jogar cada valor nas variáveis de forma limpa.
            [$nome, $sobrenome, $ddd, $telefone, $email] = array_map([Util::class, "preparaTexto"], array_values($_POST));
            
            try{
                // Tenta criar o objeto do Cliente na memória RAM usando os dados que vieram do POST.
                $cliente = Cliente::criar(null, (int)$ddd, (int)$telefone, $nome, $sobrenome, $email);
                
                // Manda o DAO inserir esse cliente no banco de dados e retorna o ID gerado.
                $id = ClienteDao::cadastrar($cliente);

                // 'header("Location: ...")' manda o servidor enviar uma ordem para o navegador do usuário
                // mandando ele abrir outra página. Isso evita que o formulário seja re-enviado se o usuário apertar F5.
                header("Location: ?p=cad");
                exit; // O exit é igual o exit() do C, interrompe a execução do código na hora.

            } catch (\Exception $e){
                // Se o Model recusar um dado inválido e "explodir" um erro, caímos aqui e guardamos a mensagem do erro.
                self::$msg = $e->getMessage();
            }
        }

        // Chama o método estático da tela (View) de formulário, e passa a mensagem de erro se houver.
        clienteView::formulario(self::$msg);
    }

    public static function editar(): void {
        $cliente = null;
        // $_GET é a GLOBAL que lê coisas da URL. Se acessar "?alt=5", isset($_GET["alt"]) é verdadeiro.
        if (isset($_GET["alt"])) {
            // Pede para o DAO buscar no banco o cliente que tenha o ID passado na URL.
            $cliente = ClienteDao::buscarPorId((int)$_GET["alt"]);
        }

        // Caso o usuário tenha preenchido o form e clicado em "Confirmar" (gerando um POST que tem o campo "id").
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            [$id, $nome, $sobrenome, $ddd, $telefone, $email] = array_map([Util::class, "preparaTexto"], array_values($_POST));

            try {
                // Cria o objeto cliente (mas agora passando o ID já existente, não é null).
                $cliente = Cliente::criar((int)$id, (int)$ddd, (int)$telefone, $nome, $sobrenome, $email);
                // Manda atualizar no banco.
                ClienteDao::editar($cliente);
                // Redireciona para a tela de listar.
                header("Location: ?p=list");
                exit;
            } catch (Exception $e) {
                self::$msg = $e->getMessage();
            }
        }

        // Exibe o formulário. Se buscar no banco deu certo, $cliente estará preenchido e a View vai preencher os inputs.
        clienteView::formulario(self::$msg, $cliente);
    }

    public static function listar(?int $deletar = null) : void {
        // Decide como ordenar. Se não houver a variável 'order' na URL ($_GET), o padrão "getId" será usado.
        // O match vai mapear o parâmetro de texto para o nome da função Getter do Cliente que será usada para ler a propriedade.
        $ordenar = match($_GET["order"] ?? ""){
            "nome" => "getNome",
            "sobrenome" => "getSobrenome",
            "ddd" => "getDdd",
            "telefone" => "getTelefone",
            "email" => "getEmail",
            default => "getId"
        };

        // Solicita ao banco de dados o array contendo a lista inteira de Clientes.
        $clientes = ClienteDao::listar();
        
        // 'usort' é o equivalente ao qsort do C, ordena os itens do array baseando-se numa função de comparação.
        // O operador espacial '<=>' devolve -1, 0 ou 1, organizando os itens de forma crescente comparando os valores lidos pelos getters (ex: $cliente->getNome()).
        usort($clientes, fn($a, $b) => $a->$ordenar() <=> $b->$ordenar());
        
        // Dispara a View 'listar', entregando o array de clientes já ordenado para que ela apenas os desenhe em tela.
        clienteView::listar($clientes, $deletar);
    }

    public static function buscarClientePorId(int $id) : ?Cliente {
        return ClienteDao::buscarPorId($id);
    }

    public static function deletar() : void {
        // Se houver a variável 'del' na URL...
        if (isset($_GET["del"])) {
            // ... a intenção do usuário é apenas ver a caixa de confirmação. 
            // Então chamamos a página de listar, mas ativando a caixinha do deletar com o ID alvo.
            self::listar((int)$_GET["del"]);
        }

        // Se houver a variável 'deletar' na URL...
        if (isset($_GET["deletar"])) {
            // ... significa que o usuário já clicou no botão de confirmação e agora vamos realmente deletar do banco.
            ClienteDao::excluir((int)$_GET["deletar"]);
            // Redireciona a tela limpando os parâmetros perigosos.
            header("Location: ?p=list");
            exit;
        }
    }
}

<?php
namespace App\View;

use App\Model\Cliente;

// A View é a camada que gera as telas.
// Em padrão MVC puro, a View não busca coisas no banco, ela apenas recebe variáveis do Controller 
// e sabe "desenhá-las" em formato HTML.
class clienteView{
    
    // Método que gera o HTML da tabela de clientes.
    // Recebe o array de clientes prontinho do Controller.
    public static function listar($clientes, ?int $deletar = null): void {
    // Se a variável $deletar tiver um valor, significa que devemos imprimir a caixa de confirmação.
    // A sintaxe de "if(): ... endif;" é muito usada no PHP junto do HTML para ficar mais limpo que as "{}".
    if ($deletar !== null): ?>
    <div class="alert">
        Você deseja realmente deletar?
        <!-- <?= $deletar ?> joga o ID do usuário direto na URL do link de Confirmar -->
        <a href="?p=deletar&deletar=<?= $deletar ?>">Confirmar</a> | 
        <a href="?p=list">Cancelar</a>
        <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
    </div>
    <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <!-- Esses links definem a variável $_GET["order"] para mudar a ordenação no Controller -->
                    <th><a href="?p=list&order=id">Id</a></th>
                    <th><a href="?p=list&order=nome">Nome</a></th>
                    <th><a href="?p=list&order=sobrenome">Sobrenome</a></th>
                    <th><a href="?p=list&order=ddd">DDD</a></th>
                    <th><a href="?p=list&order=telefone">Telefone</a></th>
                    <th><a href="?p=list&order=email">Email</a></th>
                    <th>Alterar</th>
                    <th>Deletar</th>
                </tr>
            </thead>
            <tbody>
<?php 
// Percorre o array de clientes. É como um loop for que vai printando as colunas da tabela.
foreach($clientes as $cliente): ?>
<tr>
    <!-- Chama o método Getter do objeto Cliente para ler a propriedade blindada dele -->
    <td><?= $cliente->getId() ?></td>
    <td><?= $cliente->getNome() ?></td>
    <td><?= $cliente->getSobrenome() ?></td>
    <td><?= $cliente->getDdd() ?></td>
    <td><?= $cliente->getTelefone() ?></td>
    <td><?= $cliente->getEmail() ?></td>
    <!-- Cria links para alterar e excluir adicionando na URL as variáveis com o ID do cliente específico -->
    <td><a href="?p=alt&alt=<?= $cliente->getId() ?>">Alterar</a></td>
    <td><a href="?p=deletar&del=<?= $cliente->getId() ?>">Excluir</a></td>
</tr>
<?php endforeach;?>
            </tbody>
        </table>
        <?php
    }
    
    // Método que desenha a tela de Formulário (usada tanto pra cadastrar novo quanto pra editar antigo).
    public static function formulario(?string $msg, ?Cliente $cliente = null) : void {
        // Se houver uma mensagem de erro que veio do Controller, a exibe.
        if ($msg !== null): ?>
        <div class="alert">
            <?= $msg ?>
        <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        
        <?php endif; ?>
        
    <!-- method="post" indica que os dados não vão aparecer na URL do navegador, mas sim dentro do corpo invisível da requisição (que será lida na variável global $_POST). 
         Se o objeto $cliente existir, significa que é o modo "Alterar" e mandará o form para a rota ?p=alt. Se não, é "Cadastrar" na rota ?p=cad. -->
    <form action="<?= isset($cliente)? "?p=alt": "?p=cad" ?>" method="post">
        <?php 
        // O campo ID só é desenhado se estivemos editando um cliente já existente, não precisamos de ID para novo cadastro.
        if(isset($cliente)): ?>
        <label for="id">Id:</label>
        <input type="text" name="id" id="id" value="<?= $cliente->getId() ?>" readonly>
        <?php endif; ?>

        <!-- 'name="nome"' é muito importante! É esse nomezinho que será a chave dentro do array GLOBAL $_POST no Controller.
             'value' preenche a caixinha do input. Se tiver o cliente (edição), traz os dados dele. Se não (novo form vazio), deixa "". -->
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?= isset($cliente)?$cliente->getNome() : "" ?>">

        <label for="sobrenome">Sobrenome:</label>
        <input type="text" name="sobrenome" id="sobrenome" value="<?= isset($cliente)? $cliente->getSobrenome() : "" ?>" >

        <label for="ddd">DDD:</label>
        <input 
            type="number" 
            name="ddd" 
            id="ddd" 
            value="<?= isset($cliente)? $cliente->getDdd() : "" ?>" >

        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" id="telefone" value="<?= isset($cliente)? $cliente->getTelefone() : "" ?>" >

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?= isset($cliente)? $cliente->getEmail() : "" ?>" >        

        <!-- O botão dinamicamente muda de texto dependendo da operação -->
        <button type="submit" name="enviaForm">
           <?= isset($cliente)? "Confirmar" : "Salvar" ?>
        </button>
    </form>
    <?php
    }
}
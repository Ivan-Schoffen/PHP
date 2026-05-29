<?php
namespace App\View;

use App\Model\Cliente;

class clienteView{
    public static function listar($clientes, ?int $deletar = null): void {
    if ($deletar !== null): ?>
    <div class="alert">
        Você deseja realmente deletar?
        <a href="?p=deletar&deletar=<?= $deletar ?>">Confirmar</a> | 
        <a href="?p=list">Cancelar</a>
        <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
    </div>
    <?php endif; ?>
        <table>
            <thead>
                <tr>
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
<?php foreach($clientes as $cliente): ?>
<tr>
    <td><?= $cliente->getId() ?></td>
    <td><?= $cliente->getNome() ?></td>
    <td><?= $cliente->getSobrenome() ?></td>
    <td><?= $cliente->getDdd() ?></td>
    <td><?= $cliente->getTelefone() ?></td>
    <td><?= $cliente->getEmail() ?></td>
    <td><a href="?p=alt&alt=<?= $cliente->getId() ?>">Alterar</a></td>
    <td><a href="?p=deletar&del=<?= $cliente->getId() ?>">Excluir</a></td>
</tr>
<?php endforeach;?>
            </tbody>
        </table>
        <?php
    }
    public static function formulario(?string $msg, ?Cliente $cliente = null) : void {
        if ($msg !== null): ?>
        <div class="alert">
            <?= $msg ?>
        <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        
        <?php endif; ?>
        
    <form action="<?= isset($cliente)? "?p=alt": "?p=cad" ?>" method="post">
        <?php if(isset($cliente)): ?>
        <label for="id">Id:</label>
        <input type="text" name="id" id="id" value="<?= $cliente->getId() ?>" readonly>
        <?php endif; ?>

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

        <button type="submit" name="enviaForm">
           <?= isset($cliente)? "Confirmar" : "Salvar" ?>
        </button>
    </form>
    <?php
    }
}
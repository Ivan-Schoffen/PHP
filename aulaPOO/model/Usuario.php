<?php
namespace App\Model;

class Usuario{
    private int $id;
    private string $email, $senha;

    public function __Construct($id=0, $email="", $senha=""){
        $this->id = $id;
        $this->email = $email;
        $this->senha = $senha;
    }

    public function __get($atributo){
        return $this->$atributo;
    }
    public function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

}
<?php

namespace Model;

class Usuario extends ActiveRecord{

    //Base datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','apellido','email','password','telefono','admin','confirmado','token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function  __construct($args=[]){
        $this->id=$args['id'] ?? null;
        $this->nombre=$args['nombre'] ?? '';
        $this->apellido=$args['apellido'] ?? '';
        $this->email=$args['email'] ?? '';
        $this->password=$args['password'] ?? '';
        $this->telefono=$args['telefono'] ?? '';
        $this->admin=$args['admin'] ?? 0;
        $this->confirmado=$args['confirmado'] ?? 0;
        $this->token=$args['token'] ?? '';
    }

    //Mensajes de validacion para la creacion de una cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][]='El Nombre es Obligatorio';
        }

        if(!$this->apellido){
            self::$alertas['error'][]='El Apellido es Obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][]='El E-mail es Obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][]='El Password es Obligatorio';
        }

        //Longitud del password. strlen retonar la longitud de un string
        if(strlen($this->password<6)){
            self::$alertas['error'][]='El Password Debe Contener al Menos 6 Caracteres';
        }

        if(!$this->telefono){
            self::$alertas['error'][]='El Telefono es Obligatorio';
        }






        return self::$alertas;
    }
}
<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class  LoginController{
    public static function login(Router $router){
        $router->render('auth/login');
    }

    public static function logout(){
        echo 'Desde logout';
    }

    public static function olvide(Router $router){
        $router->render('auth/olvide',[

        ]);
    }

    public static function recuperar(){
        echo 'Desde recuperar';
    }

    public static function crear(Router $router){

        $usuario=new Usuario($_POST);

        if($_SERVER['REQUEST_METHOD']==='POST'){

        }
        $router->render('auth/crear-cuenta',[
            'usuario'=>$usuario
        ]);
    }
}

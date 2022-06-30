<?php

namespace Controllers;

use Classes\Email;
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

        //Alertas vacias
        $alertas=[];

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que alertas este vacio
            if(empty($alertas)){
                //Verificar que el usuario no esta registrado
                $resultado=$usuario->existeUsuario();

                if ($resultado->num_rows){
                    $alertas= Usuario::getAlertas();
                }else{
                    //Hashear el password
                    $usuario->hashPassword();

                    //Generar un token unico
                    $usuario->crearToken();

                    //Enviar el Email
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);

                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado=$usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }
                    
                }
            }

        }
        $router->render('auth/crear-cuenta',[
            'usuario'=>$usuario,
            'alertas'=>$alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }
}

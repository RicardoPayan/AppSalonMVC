<?php

namespace Controllers;

use Classes\Email;
use Couchbase\UpsertUserOptions;
use Model\Usuario;
use MVC\Router;

class  LoginController{
    public static function login(Router $router){
        $alertas = [];



        if($_SERVER['REQUEST_METHOD']==='POST'){
            $auth = new Usuario($_POST);
            $alertas=$auth->validarLogin();

            if(empty($alertas)){
                //Significa que los usuarios agrego tanto email como password
                //Comprobar que exista el usuario
                $usuario = Usuario::where('email',$auth->email);
                if($usuario){
                    //Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        //Autenticar al usuario
                        session_start();
                        $_SESSION['id']=$usuario->id;
                        $_SESSION['nombre']=$usuario->nombre." ". $usuario->apellido;
                        $_SESSION['email']=$usuario->email;
                        $_SESSION['login']=true;

                        //Redireccionar
                        if($usuario->admin=='1'){
                           $_SESSION['admin']=$usuario->admin ?? null;
                            header('Location: /admin');
                        }else{
                            header('Location: /cita');
                        }

                        debuguear($_SESSION);
                    }

                }else{
                    Usuario::setAlerta('error','Usuario no encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login',[
            'alertas' => $alertas,

        ]);
    }

    public static function logout(){
        echo 'Desde logout';
    }

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $auth = new Usuario($_POST);
            $alertas=$auth->validarEmail();

            if(empty($alertas)){
                //Verificar si el email existe
                $usuario = Usuario::where('email',$auth->email);
                 if($usuario && $usuario->confirmado ==='1'){
                     //Si existe y esta confirmado
                     //Generar Token
                     $usuario->crearToken();
                     $usuario->guardar();

                     //Enviar Email
                     $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                     $email->enviarInstrucciones();
                     //Alerta de exito
                     Usuario::setAlerta('exito','Revisa tu Email');

                 }else{
                     Usuario::setAlerta('error','Usuario no encontrado o no esta confirmado');
                 }
            }
        }

        $alertas=Usuario::getAlertas();

        $router->render('auth/olvide',[
            'alertas'=>$alertas
        ]);
    }

    public static function recuperar(Router $router){
        $alertas=[];
        $error = false;
        $token = s($_GET['token']);


        //Buscar Usuario por su token
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)){
            Usuario::setAlerta('error','Token No valido');
            $error=true;
        }


        if($_SERVER['REQUEST_METHOD']==='POST'){
            //Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas=$password->validarPassword();

            if(empty($alertas)){

                //Eliminamos el password anterior
                $usuario->password = null;

                //Al usuario que tenemos en memoria le asignamos el nuevo password, que tenemos guarado en la instancia $password
                $usuario->password= $password->password;
                //Hasheamos
                $usuario->hashPassword();
                //Borramos el token
                $usuario->token='';

                $resultado=$usuario->guardar();

                if ($resultado){
                    header('Location: /');
                }


            }
        }



        $alertas=Usuario::getAlertas();

        $router->render('auth/recuperar-password',[
            'alertas'=>$alertas,
            'error'=>$error
        ]);
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

    public static function confirmar(Router $router){
        $alertas=[];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)){
            //mostrar mensaje de error
            Usuario::setAlerta('error','Token No Válido');
        }else{
            //Usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = '';
            $usuario->guardar();

            Usuario::setAlerta('exito','Cuenta Validada');
        }

        //Renderizar la vista
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta',[
            'alertas'=>$alertas
        ]);
    }
}

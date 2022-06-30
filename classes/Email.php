<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email,$nombre,$token){
        $this->email=$email;
        $this->nombre=$nombre;
        $this->token=$token;
    }

    public function enviarConfirmacion(){
        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'a9c0aaf1c3a8fa';
        $mail->Password = 'ce5ea634d2401d';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com','AppSalon.com');
        $mail->Subject='Confirma tu cuenta';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        //Utilizando HTML para el body del correo
        $contenido = "<html>";
        $contenido.= "<p><strong>Hola ". $this->nombre ." </strong>Has creado tu cuenta en AppSalon solo debes
        confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
        $contenido.="<p>Si tu no solicitaste esta cuenta puedes ignorar el mensaje</p>";
        $contenido.="</html>";

        $mail->Body=$contenido;

        //Enviar Email
        $mail->send();
    }
}
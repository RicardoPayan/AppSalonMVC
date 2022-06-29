<h1 class="nombre-pagina">Recuperar Cuenta</h1>
<p class="descripcion-pagina">Ingresa tu E-mail para reestablecer tu contraseña</p>

<form class="formulario" method="POST" action="/olvide">
    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Tu E-mail">
    </div>

    <input type="submit" class="boton" value="Enviar Instrucciones">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta"> ¿Aún no tienes una cuenta? Crear Una</a>
</div>
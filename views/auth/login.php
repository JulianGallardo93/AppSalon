<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia Sesión con tus Datos</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

<form action="/" class="formulario" method="POST">
    <div class="campo">
    <label for="email">Email</label>
    <input type="email" id="email" placeholder="Tu Email" name="email">
    </div>

    <div class="campo">
    <label for="password">password</label>
    <input type="password" id="password" placeholder="Tu password" name="password">
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>
<div class="acciones">
<a href="/crear-cuenta">Crear tu cuenta</a>
<a href="/olvide">Recuperar contraseña</a>
</div>
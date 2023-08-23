<h1 class="nombre-pagina">Olvide mi contraseña</h1>
<p class="descripcion-pagina">Escribe tu correo electronico para reucperar la contraseña</p>


<?php 
    include_once __DIR__ . '/../templates/alertas.php'; 
?>


<form action="/olvide" class="formulario" method="POST">
<div class="campo">
    <label for="email">Email</label>
    <input type="email" id="email" placeholder="Tu Email" name="email">
    </div>

    <input type="submit" class="boton" value="Recuperar Contraeña">
</form>

<div class="acciones">
<a href="/crear-cuenta">Crear una Cuenta Nueva</a>
<a href="/">Volver</a>
</div>
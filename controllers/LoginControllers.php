<?php
    namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginControllers {

    
    public static function login(Router $router){

        $alertas = [];

        

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

           $alertas = $auth->validarLogin();
            if(empty($alertas)){
                //comprobar que exista
                $usuario = Usuario::where('email', $auth->email);

                    if($usuario){
                        //verificar password
                        if($usuario->comprobarPasswordAndVerificado($auth->password)){
                            //auth de usuario
                            session_start();
                            
                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            //redireccionar
                            if($usuario->admin === "1"){
                                $_SESSION['admin'] = $usuario->admin ?? null;

                                header('Location: /admin');
                            }else{
                                header('Location: /cita');
                            }
                            
                        }
                    }else{
                        Usuario::setAlerta('error', 'Usuario no encontrado');
                    }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === '1'){
                    //generar token
                    $usuario->crearToken();
                    $usuario->guardar();
                    //Enviar email con token
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //alerta
                    Usuario::setAlerta('exito', 'Comrpueba tu Email');
                    
                }else{
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' =>$alertas
        ]);
    }

    public static function recuperar(Router $router){

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'token no valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] ==='POST'){
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router){

        $usuario = new Usuario($_POST);

        //alertas vacias
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //revisar que alertas este vacio
            if(empty($alertas)){
                //que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{
                    $usuario->hashPassword();
                    $usuario->crearToken();
                    //enviar emails
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    //confirmar email
                    $email->enviarConfirmacion();

                    //debuguear($email);

                    //crear usuario
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header('location: /mensaje');
                    }
                    
                }
            }

            
        }
        $router->render('auth/crear-cuenta',[
            'usuario' => $usuario,
            'alertas' =>$alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'validacion no valida');
        }else{
            $usuario->confirmado = '1';
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'cuenta confirmada correctamente');
        }


        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
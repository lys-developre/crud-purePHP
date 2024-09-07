<?php
// Iniciar la sesión para manejar errores y otros datos que puedan ser necesarios.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Incluir los archivos requeridos: el modelo de usuario y la configuración de manejo de errores.
require_once __DIR__ . '/../modelos/UsuarioModelo.php';
require_once __DIR__ . '/../config/errores.php';




// Clase controladora para manejar la validacion y el registro de usuarios.
class UsuarioControlador
{



    
    public function registrarUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrarse'])) {
            // Crear una instancia del modelo UsuarioModelo
            $modelo = new UsuarioModelo();

            // Validar los datos del formulario y almacenar en errores si los hubo en la validacion.
            $errores = $modelo->validarRegistro($_POST);


            // Comprobamos si hay errores después de la validación
            // Si no hay errores en la validacion.
            if (empty($errores)) {

                //PRIMERA CONSULTA A USER_DATA
                //Insertar los datos del usuario en `users_data`
                $idUsuario = $modelo->insertarUsuario($_POST);


                //SEGUNDA CONSULTA A USER_LOGIN.
                //si la consulta en user data fue exitosa se nos genera el id_user.
                if ($idUsuario) {

                    //Insertar los datos de login en `users_login`
                    $resultadoLogin = $modelo->insertarLogin($idUsuario, $_POST['contrasena'], 'user');

                    if ($resultadoLogin) {

                        //
                        header('Location:http://localhost/Final_php/app/vistas/login.php');
                        exit();

                    } else {

                        $_SESSION['errores'] = ['insert' => 'Error al crear credenciales de inicio de sesión'];
                        header('Location: http://localhost/Final_php/app/vistas/errores/credenciales_incorrectas.php');
                        exit();
                    }
                } else {

                    //si la primera consulta no fue exitosa mostramos un error en pantalla.
                    $_SESSION['errores'] = ['insert' => 'Error al crear usuario'];
                    header('Location: http://localhost/Final_php/app/vistas/errores/error_500.php');
                    exit();
                }
            } else {
                //si hay errores en la validacion mostramos un 500 en pantalla.
                $_SESSION['errores'] = $errores;
                header('Location: http://localhost/Final_php/app/vistas/errores/error_500.php');
                exit();
            }
        }
    }
}
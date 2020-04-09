<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
require __DIR__ . '/../src/config/db.php';


return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
      $loader = new FilesystemLoader(__DIR__ . '/../vistas');
      $twig = new Environment($loader);

      $response->getBody()->write($twig->render('index.twig'));
      return $response;
    });

//LISTAR USUARIO
    $app->get('/usuarios', function (Request $request, Response $response) {
      $loader = new FilesystemLoader(__DIR__ . '/../vistas');
      $twig = new Environment($loader);

      $sql = "SELECT * FROM usuarios";
      try{
        $db = new db();
        $db = $db->conexionDB() ;
        $resultado = $db->query($sql);
        if($resultado->rowCount() > 0){
          while ( $obj = $resultado->fetch(PDO::FETCH_OBJ) ) {
            $usuarios[] = $obj;
          }
          $datos = array(
            'usuarios' => $usuarios
          );
          $response->getBody()->write($twig->render('usuarios_listados.twig', $datos));
        }else{
          $response->getBody()->write( json_encode("No existen clientes en la base de datos") );
        }
        $resultado = null;
        $db = null;
      }catch(PDOException $e){
        $response->getBody()->write( '{"ERROR" : {"text":'.$e->getMessage().'}}');
      }

      return $response;
    });

//BORRAR USUARIO
    $app->get('/usuarios/borrar/{id}', function (Request $request, Response $response, array $args) {
      $id = $args['id'];
      $sql = "DELETE FROM usuarios WHERE id = $id";


      try{
        $db = new db();
        $db = $db->conexionDB();
        $resultado = $db->prepare($sql);
         $resultado->execute();

        if ($resultado->rowCount() > 0) {
          $response->getBody()->write( json_encode("Usuario eliminado.") );
        }else {
          $response->getBody()->write( json_encode("No existe usuario con este ID.") );
        }

        $resultado = null;
        $db = null;
      }catch(PDOException $e){
        $response->getBody()->write( '{"ERROR" : {"text":'.$e->getMessage().'}}');
      }
      return $response;
    });

//REENVIA A LA PAGINA PARA INGRESAR LOS DATOS PARA MODIFICAR USUARIO
    $app->get('/usuarios/modificar/{id}', function (Request $request, Response $response, array $args) {
      $loader = new FilesystemLoader(__DIR__ . '/../vistas');
      $twig = new Environment($loader);

      $id = $request->getAttribute('id');
      $sql = "SELECT * FROM usuarios WHERE id = $id";
      try{
        $db = new db();
        $db = $db->conexionDB();
        $resultado = $db->query($sql);

        if ( $usuario = $resultado->fetch(PDO::FETCH_OBJ) ){
          $datos = array(
            'usuario' => $usuario
          );
          $response->getBody()->write($twig->render('usuario_modificar.twig', $datos));
        }else{
          $response->getBody()->write( json_encode("No existen usuario en la base de datos") );
        }
        $resultado = null;
        $db = null;
      }catch(PDOException $e){
        $response->getBody()->write( '{"ERROR" : {"text":'.$e->getMessage().'}}');
      }
      return $response;
    });

//GUARDAR LOS CAMBIOS AL USUARIO MODIFICADO
    $app->post('/usuarios/modificar/guardar/{id}', function (Request $request, Response $response, array $args) {
      $loader = new FilesystemLoader(__DIR__ . '/../vistas');
      $twig = new Environment($loader);

      $id = $request->getAttribute('id');

      $nombre = $request->getParsedBody()['nombre'];
      $apellidos = $request->getParsedBody()['apellido'];
      $ci = $request->getParsedBody()['ci'];
      $email = $request->getParsedBody()['email'];
      $pass = password_hash($request->getParsedBody()['password'], PASSWORD_DEFAULT);

     $sql = "UPDATE usuarios SET
                                nombre = :nombre,
                                apellido = :apellidos,
                                ci = :ci,
                                email = :email,
                                password = :pass
                            WHERE id = $id";
    try{
      $db = new db();
      $db = $db->conexionDB();
      $resultado = $db->prepare($sql);

      $resultado->bindParam(':nombre', $nombre);
      $resultado->bindParam(':apellidos', $apellidos);
      $resultado->bindParam(':ci', $ci);
      $resultado->bindParam(':email', $email);
      $resultado->bindParam(':pass', $pass);

      $resultado->execute();
      $response->getBody()->write( json_encode("Usuario modificado guardado.") );

     $resultado = null;
     $db = null;
   }catch(PDOException $e){
     $response->getBody()->write( '{"error" : {"text":'.$e->getMessage().'}}' );
   }

      return $response;
    });

//ME DIRECCIONA A LA PAGINA PARA AGREGAR USUARIO
    $app->get('/usuarios/agregar', function (Request $request, Response $response) {
      $loader = new FilesystemLoader(__DIR__ . '/../vistas');
      $twig = new Environment($loader);
      $response->getBody()->write($twig->render('usuario_agregar.twig'));
      return $response;
    });

//SE REGISTRA UN USUARIO NUEVO
// POST Crear nuevo usuario
    $app->post('/usuarios/agregar/registrar', function (Request $request, Response $response) {
      $loader = new FilesystemLoader(__DIR__ . '/../vistas');
      $twig = new Environment($loader);

      $nombre = $request->getParsedBody()['nombre'];
      $apellidos = $request->getParsedBody()['apellido'];
      $ci = $request->getParsedBody()['ci'];
      $email = $request->getParsedBody()['email'];
      //la funcion sha1 codifica la contrasña
      $pass = password_hash($request->getParsedBody()['password'], PASSWORD_DEFAULT);

     $sql = "INSERT INTO usuarios (nombre, apellido, ci, email, password) VALUES
             (:nombre, :apellidos, :ci, :email, :pass)";

    try{
      $db = new db();
      $db = $db->conexionDB();
      $resultado = $db->prepare($sql);

      $resultado->bindParam(':nombre', $nombre);
      $resultado->bindParam(':apellidos', $apellidos);
      $resultado->bindParam(':ci', $ci);
      $resultado->bindParam(':email', $email);
      $resultado->bindParam(':pass', $pass);

      $resultado->execute();
      $response->getBody()->write( json_encode("Nuevo usuario guardado.") );

     $resultado = null;
     $db = null;
   }catch(PDOException $e){
     $response->getBody()->write( '{"error" : {"text":'.$e->getMessage().'}}' );
   }
      return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});
};

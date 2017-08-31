<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', '', 'angular4p0');

$app->get("/test", function () use ($app){
  echo "Hello world from Slim PHP";
});

$app->get("/employees", function () use ($app){
  echo "Hello employees";
});

// SAVE PRODUCTS
$app->post('/products', function() use($app, $db){

  $json = $app->request->post('json');
  $data = json_decode($json, true);

  if(!isset($data['nombre'])){
    $data['nombre']=null;
  }

  if(!isset($data['descripcion'])){
    $data['descripcion']=null;
  }

  if(!isset($data['precio'])){
    $data['precio']=null;
  }

  if(!isset($data['imagen'])){
    $data['imagen']=null;
  }

  $query = "INSERT INTO productos VALUES(NULL,".
  "'{$data['nombre']}',".
  "'{$data['descripcion']}',".
  "'{$data['precio']}',".
  "'{$data['imagen']}'".
  ");";
  
  $insert = $db->query($query);

  $result = array(
    'status' => 'error',
    'code'	 => 404,
    'message' => 'Producto NO se ha creado'
  );

  if($insert){
    $result = array(
      'status' => 'success',
      'code'	 => 200,
      'message' => 'Producto creado correctamente'
    );
  }

  echo json_encode($result);

});

$app->run();

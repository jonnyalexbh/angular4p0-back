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

// LIST ALL PRODUCTS
$app->get('/products', function() use($db, $app){
  $sql = 'SELECT * FROM productos ORDER BY id DESC;';
  $query = $db->query($sql);

  $productos = array();
  while ($producto = $query->fetch_assoc()) {
    $productos[] = $producto;
  }

  $result = array(
    'status' => 'success',
    'code'	 => 200,
    'data' => $productos
  );

  echo json_encode($result);
});

// RETURN ONE PRODUCT
$app->get('/products/:id', function($id) use($db, $app){
  $sql = 'SELECT * FROM productos WHERE id = '.$id;
  $query = $db->query($sql);

  $result = array(
    'status' 	=> 'error',
    'code'		=> 404,
    'message' 	=> 'Producto no disponible'
  );

  if($query->num_rows == 1){
    $producto = $query->fetch_assoc();

    $result = array(
      'status' 	=> 'success',
      'code'		=> 200,
      'data' 	=> $producto
    );
  }

  echo json_encode($result);
});

// REMOVE A PRODUCT
$app->get('/delete-product/:id', function($id) use($db, $app){
  $sql = 'DELETE FROM productos WHERE id = '.$id;
  $query = $db->query($sql);

  if($query){
    $result = array(
      'status' 	=> 'success',
      'code'		=> 200,
      'message' 	=> 'El producto se ha eliminado correctamente!!'
    );
  }else{
    $result = array(
      'status' 	=> 'error',
      'code'		=> 404,
      'message' 	=> 'El producto no se ha eliminado!!'
    );
  }

  echo json_encode($result);
});

// UPDATE A PRODUCT
$app->post('/update-product/:id', function($id) use($db, $app){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$sql = "UPDATE productos SET ".
		   "nombre = '{$data["nombre"]}', ".
		   "descripcion = '{$data["descripcion"]}', ";

	if(isset($data['imagen'])){
 		$sql .= "imagen = '{$data["imagen"]}', ";
	}

	$sql .=	"precio = '{$data["precio"]}' WHERE id = {$id}";


	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El producto se ha actualizado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El producto no se ha actualizado!!'
		);
	}

	echo json_encode($result);

});

// UPLOAD AN IMAGE TO A PRODUCT
$app->post('/upload-file', function() use($db, $app){
	$result = array(
		'status' 	=> 'error',
		'code'		=> 404,
		'message' 	=> 'El archivo no ha podido subirse'
	);

	if(isset($_FILES['uploads'])){
		$piramideUploader = new PiramideUploader();

		$upload = $piramideUploader->upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];

		if(isset($upload) && $upload["uploaded"] == false){
			$result = array(
				'status' 	=> 'error',
				'code'		=> 404,
				'message' 	=> 'El archivo no ha podido subirse'
			);
		}else{
			$result = array(
				'status' 	=> 'success',
				'code'		=> 200,
				'message' 	=> 'El archivo se ha subido',
				'filename'  => $file_name
			);
		}
	}

	echo json_encode($result);
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

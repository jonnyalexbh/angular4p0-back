<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->get("/test", function () use ($app){
  echo "Hello world from Slim PHP";
});

$app->get("/employees", function () use ($app){
  echo "Hello employees";
});

$app->run();

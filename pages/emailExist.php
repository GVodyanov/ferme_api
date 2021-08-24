<?php
  //header per RESTful api
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  //ci prendiamo $conn per eseguire le connessioni e san() per sanificare input e emailExist per lo scopo principale
  require_once "../services/connect.php";
  require_once "../services/sanitize.php";
  require_once "../services/emailExistFn.php";

  // Partenza e Verifica connessione
  if ($conn->connect_error) {
    die("Errore di connessione!");
  }

  //prendiamo i dati GET
  $email = san($conn, $_GET['email']);

  //e eseguiamo la funzione
  echo json_encode(emailExist($conn, $email));

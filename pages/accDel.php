<?php
  //header per RESTful api
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  //ci prendiamo $conn per eseguire le connessioni e san() per sanificare input
  require_once "../services/connect.php";
  require_once "../services/sanitize.php";

  // Partenza e Verifica connessione
  if ($conn->connect_error) {
    die("Errore di connessione!");
  }

  //prendiamo il password e l'id di cui cancellare l'account
  $password = hash('sha256', $_GET['password']);
  $id = san($conn, $_GET['id']);

  //e adesso vediamo se il password appartiene al id e eseguiamo il query, poi diamo true o false in base a se il
  //query ha funzionato
  $sql = "DELETE FROM utenti WHERE user_id = $id AND password = '$password'";
  if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');

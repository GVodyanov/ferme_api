<?php
  //header per RESTful api
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  //ci prendiamo $conn per eseguire le connessioni
  require_once '../services/connect.php';
  require_once "../services/sanitize.php";

  // Partenza e Verifica connessione
  if ($conn->connect_error) {
    die("Errore di connessione!");
  }

  //per questo file ci servira' l'id di quello che manda, il messaggio che manda, e in che forum verra' mandato
  $id = san($conn, $_GET['id']);
  $message = san($conn, $_GET['message']);
  $request = san($conn, $_GET['selection']);

  //prima di tutto vorrei fare dei controlli sul messaggio
  if (strlen($message) < 1025) {
    $sql = "INSERT INTO $request (messaggi, id) VALUES ('$message', '$id')";
    if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
  }

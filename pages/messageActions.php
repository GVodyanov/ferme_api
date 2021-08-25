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

  //prima di tutto dobbiamo capire che azione vogliono eseguire, le possibilita' sono cancellare, segnalare, o modificare
  $action = san($conn, $_GET['action']);
  $id = san($conn, $_GET['id']);
  $request = san($conn, $_GET['selection']);
  $messageId = san($conn, $_GET['messageId']);

  //questa e' la sezione che si dedica al cancellare i messaggi
  if ($action == 'delete') {

    //prima dobbiamo confermare che il messaggio appartiene veramente alla persona
    $sql = "SELECT data FROM $request WHERE numeratore = $messageId AND id = $id";
    $result = $conn->query($sql);
    $result = mysqli_fetch_assoc($result);

    //adesso che abbiamo confermato che tutto e' in ordine possiamo eseguire il query
    if ($result['data'] != NULL) {
      $sql = "DELETE FROM $request WHERE numeratore = $messageId";
      if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
    } else echo json_encode('false');
  }

  //questa e' la sezione che si dedica al modificare i messaggi
  if ($action == 'modify') {

    //ci servira' scoprire a quale messaggio si vuole cambiare
    $newMessage = san($conn, $_GET['newMessage']);

    //possiamo fare la stessa confermazione di prima
    $sql = "SELECT data FROM $request WHERE numeratore = $messageId AND id = $id";
    $result = $conn->query($sql);
    $result = mysqli_fetch_assoc($result);

    //adesso che abbiamo confermato che tutto e' in ordine possiamo eseguire il query
    if ($result['data'] != NULL) {
      $sql = "UPDATE $request SET messaggi = '$newMessage' WHERE numeratore = $messageId";
      if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
    } else echo json_encode('false');
  }

  //questa e' la sezione che si dedica al segnalare un messaggio
  if ($action == 'report') {

    //semplice query sql non c'e' niente da dire
    $sql = "INSERT INTO segnalazioni (messaggio, idSegnalatore, forum) VALUES ($messageId, $id, '$request')";
    if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');

  }

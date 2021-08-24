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

  //per questo file ci servira' soltanto il query
  $search = san($conn, $_GET['query']);

  //ci serve un counter id per controllare ogni persona
  $indexId = 1;

  //c'e' una possibilita' che due persone hanno lo stesso username (ferme ha solo l'email e l'id che devono essere unici)
  //percio' vogliamo fare un array che puo' accomodare questo fatto, utilizzando un index nella prima dimensione
  $returnIndex = 0;

  //prima di tutto dobbiamo capire quante persone sono registrare per loopare su ogni persona
  $sql = "SELECT MAX(user_id) AS MaxPersone FROM utenti";
  $result = $conn->query($sql);
  $result = mysqli_fetch_assoc($result);
  $persone = $result['MaxPersone'];

  //questo e' naturalmente un loop che cerca ogni persona
  while($indexId <= $persone){

    //qui facciamo un query dove prendiamo il username di ogni persona e poi lo confonteremo con $search
    $sql = "SELECT username FROM utenti WHERE user_id=$indexId";
    $result = $conn->query($sql);
    $result = mysqli_fetch_assoc($result);
    $username = $result['username'];

    //qui usando il solito preg_match vediamo se $username e' contenuto in $search
    if(preg_match("/{$username}/i", $search) && $username != NULL){

      //adesso che abbiamo verificato che abbiamo trovato la persona giusta possiamo mandare i loro dati
      $sql = "SELECT picture FROM utenti WHERE user_id=$indexId";
      $result = $conn->query($sql);
      $result = mysqli_fetch_assoc($result);

      //assegniamo i valori a $return
      $return[$returnIndex]['username'] = $username;
      $return[$returnIndex]['picture'] = $result['picture'];
      $return[$returnIndex]['id'] = $indexId;

      //aumentiamo $returnIndex per evitare possibili conflitti
      $returnIndex++;
    }
    $indexId++;
  }

  //e siamo pronti a mandare i dati, o la mancanza dei dati
  echo json_encode($return);
  if ($return == NULL) echo json_encode('false');

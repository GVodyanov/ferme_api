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

  //prendiamo il forum selezionato dal cliente attraverso GET
  $request = san($conn, $_GET["selection"]);

  //facciamo il query al database
  //questo e' il commando che eseguiremo
  $sql = "SELECT id, messaggi, data, numeratore FROM $request";

  //qui eseguiamo il query per prendere i dati
  $result = $conn->query($sql);

  //adesso dobbiamo prendere quei dati e categorizzarli in un array bidimensionale
  if ($result->num_rows > 0) {

      //return e' l'array che manderemo indietro al cliente
      $return = array();

      //questo index ci serve per dare un ordine ai messaggi
      $i = 0;

      //qui abbiamo un loop che prendere ogni risultato e lo mettera' sotto al index giusto
      while($row = $result->fetch_assoc()) {

        //qui mettiamo i dati in $return
        $return[$i]['message'] = $row['messaggi'];
        $return[$i]['date'] = $row['data'];
        $return[$i]['userId'] = $row['id'];
        $return[$i]['messageId'] = $row['numeratore'];

        //un'altra piccola cosa che dobbiamo fare e' scoprire il username e foto profilo dal user_id
        $id = $row['id'];
        $query = mysqli_query($conn, "SELECT username, picture FROM utenti WHERE user_id = $id");
        $fromObj = mysqli_fetch_assoc($query);
        $return[$i]['username'] = $fromObj['username'];
        $return[$i]['picture'] = $fromObj['picture'];

        //per finire $return dobbiamo capire se un messaggio ha delle segnalazioni
        $messageId = $row['numeratore'];
        $sqlRep = "SELECT idSegnalatore FROM segnalazioni WHERE forum = '$request' AND messaggio = $messageId";
        $reports = $conn->query($sqlRep);
        $return[$i]['reports'] = $reports->num_rows;

        $i++;
        if ($i > 39) break;
      }
  } else {
      echo "0 risultati";
  }

  //adesso siamo pronti a mandare indietro i dati in formato JSON
  echo json_encode($return);

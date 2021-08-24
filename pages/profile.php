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

  //prendiamo l'id attraverso POST
  $id = san($conn, $_GET['id']);

  //adesso prendiamo i dati legati al id
  $sql = "SELECT username, email, picture, description FROM utenti WHERE user_id = $id";
  $result = $conn->query($sql);
  $return = mysqli_fetch_assoc($result);

  //ora la parte difficile, le statistiche

  //recupero del numero di messaggi inviati nei forum
  $sql="select count(*) as total from anime where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $anime = $data['total'];

  $sql="select count(*) as total from arte where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $arte = $data['total'];

  $sql="select count(*) as total from cinema where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $cinema = $data['total'];

  $sql="select count(*) as total from compiti where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $compiti = $data['total'];

  $sql="select count(*) as total from cucina where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $cucina = $data['total'];

  $sql="select count(*) as total from gaming where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $gaming = $data['total'];

  $sql="select count(*) as total from hobby where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $hobby = $data['total'];

  $sql="select count(*) as total from informatica where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $informatica = $data['total'];

  $sql="select count(*) as total from musica where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $musica = $data['total'];

  $sql="select count(*) as total from sport where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $sport = $data['total'];

  $sql="select count(*) as total from altro where id='$id'";
  $result=mysqli_query($conn,$sql);
  $data=mysqli_fetch_assoc($result);
  $altro = $data['total'];

  //totale messaggi
  $tot = $anime + $arte + $cinema + $compiti + $cucina + $gaming + $hobby + $informatica + $musica + $sport + $altro;

  //funzione per calcolare percentuali
  function percent($var, $tot){
    if($var == 0){
      $result = 0;
    } else {
      round($result = $var * 100 / $tot, 2);
    }
    return $result;
  }

  //assegniamo i nostri risultati, in percentuali, a $return
  $return['interests']['anime'] = round(@percent($anime, $tot));
  $return['interests']['arte'] = round(@percent($arte, $tot));
  $return['interests']['cinema'] = round(@percent($cinema, $tot));
  $return['interests']['compiti'] = round(@percent($compiti, $tot));
  $return['interests']['cucina'] = round(@percent($cucina, $tot));
  $return['interests']['gaming'] = round(@percent($gaming, $tot));
  $return['interests']['hobby'] = round(@percent($hobby, $tot));
  $return['interests']['informatica'] = round(@percent($informatica, $tot));
  $return['interests']['musica'] = round(@percent($musica, $tot));
  $return['interests']['sport'] = round(@percent($sport, $tot));
  $return['interests']['altro'] = round(@percent($altro, $tot));

  //e basta praticamente possiamo mandare indietro i dati
  echo json_encode($return);

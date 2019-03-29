<html>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>image</title>
</head>
<body>
<?php 
$prefix = 'https:localhost:8080/coursphp/primael-proxy///';
?>
<!-- <img src="https:localhost:8080/coursphp/primael-proxy///images-eds-ssl.xboxlive.com/image?url=8Oaj9Ryq1G1_p3lLnXlsaZgGzAie6Mnu24_PawYuDYIoH77pJ.X5Z.MqQPibUVTchUnAj40aHvUUo7k4TwoaCpZO27NRghcEK2tizD.Eyd2wDhRaYXz0OiGUw4EmWznInThMyIISqSkVRyxQgUIdGKsQQ2ykjwRk144gwcL0Y1.tslxhfnlXWvumypxP8dGsISaldsFK8MJekAJIDuKuEGER8EcbN8hrroMSRiv09JM-&h=1080&w=1920&format=jpg" alt=""> -->
<img src="<?php echo $prefix ?>images-eds-ssl.xboxlive.com/image?url=8Oaj9Ryq1G1_p3lLnXlsaZgGzAie6Mnu24_PawYuDYIoH77pJ.X5Z.MqQPibUVTchUnAj40aHvUUo7k4TwoaCpZO27NRghcEK2tizD.Eyd2wDhRaYXz0OiGUw4EmWznInThMyIISqSkVRyxQgUIdGKsQQ2ykjwRk144gwcL0Y1.tslxhfnlXWvumypxP8dGsISaldsFK8MJekAJIDuKuEGER8EcbN8hrroMSRiv09JM-&h=1080&w=1920&format=jpg" alt="">

<?php 
// $url_image = à déterminer grâce au src de l'image (prendre ce qui a après le https:localhost:8080/coursphp/primael-proxy/// grâce à $_server ? )
// $device = à déteriner grâce à entête http user_agent

//accéder à la BDD
// ** si url pas dans la BDD:
   // télécharger l'image sur base de l'url
   // la redimensionner en 3 dimensions (320px, 640px et 1366px)
   // stocker les 3 formats sur le pc
   // remplir la table de la BDD avec l'url et les 3 chemins des images créées

// ** si url dans la bdd, afficher la bonne image pour le device identifié

// champs de notre BDD : id_image	url	chemin_mobile	chemin_tablet	chemin_pc

//nom bdd : hetic_proximage

$url = 'images-eds-ssl.xboxlive.com/image?url=8Oaj9Ryq1G1_p3lLnXlsaZgGzAie6Mnu24_PawYuDYIoH77pJ.X5Z.MqQPibUVTchUnAj40aHvUUo7k4TwoaCpZO27NRghcEK2tizD.Eyd2wDhRaYXz0OiGUw4EmWznInThMyIISqSkVRyxQgUIdGKsQQ2ykjwRk144gwcL0Y1.tslxhfnlXWvumypxP8dGsISaldsFK8MJekAJIDuKuEGER8EcbN8hrroMSRiv09JM-&h=1080&w=1920&format=jpg';

//connexion à la BDD
$pdo = new PDO(
  'mysql:host=localhost;dbname=hetic_proximage;',
  'root',
  '',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
  ]
);

$resultat = $pdo->query("SELECT url FROM images WHERE url ='$url'");
$img = $resultat->fetch(PDO::FETCH_ASSOC);

//si image dans la bdd
if ($img['url']) {
  echo 'yes';
}
//si image pas dans la bdd
else {
  echo "no";
  //définition du répertoire de stockage
  define('LOCAL_IMG_DIR' , './images/'); 

  //Configuration des images à récupérer 
  $online_images[] = array( 'online_image_url' => 'https://' . $url, 'local_img_name' => 'naruto.jpg'); 
  //Récupération des images
  foreach ($online_images as $image_to_download) { 
    $image_online_url = file_get_contents($image_to_download['online_image_url']); 
    if ($image_online_url != '') { 
      if (file_put_contents(LOCAL_IMG_DIR.$image_to_download['local_img_name'], $image_online_url)){
        echo ' image enregistrée dans: '.LOCAL_IMG_DIR.$image_to_download['local_img_name']; 
      } 
    } 
  }
  // redimensionner et ajouter dans la bdd
}



// if (!isset($url_In_DB)) {
//   function ( ) {
    
       
//     $pdo-> exec('
//     INSERT INTO images (
//     id_image,
//     url,
//     chemin_mobile,
//     chemin_tablet,
//     chemin_pc
//     ) VALUES (
//       "",
//       "$url",
//       "",
//       "",
//       "",
//     )'
// );
//   }
// }

?>
</body>
</html>
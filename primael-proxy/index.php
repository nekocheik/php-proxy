<html>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>proxy image</title>
</head>
<body>
<?php 
// Connection to the database ----------------------------------------
$pdo = new PDO(
  'mysql:host=localhost;dbname=hetic_proximage;',
  'root',
  '',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
  ]
);

// Device detection --------------------------------------------------
$useragent = $_SERVER['HTTP_USER_AGENT'];


if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $useragent)) {
  $device = 'chemin_tablet';
} elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|iphone|ipod|midp|wap|phone|android|iemobile)/i', $useragent)) {
  $device = 'chemin_mobile';
} elseif (preg_match('/windows|win32|macintosh/i', $useragent)) {
  $device = 'chemin_pc';
}

//image we need to display on the page ---------------------------------
$url = 'images-eds-ssl.xboxlive.com/image?url=8Oaj9Ryq1G1_p3lLnXlsaZgGzAie6Mnu24_PawYuDYIoH77pJ.X5Z.MqQPibUVTchUnAj40aHvUUo7k4TwoaCpZO27NRghcEK2tizD.Eyd2wDhRaYXz0OiGUw4EmWznInThMyIISqSkVRyxQgUIdGKsQQ2ykjwRk144gwcL0Y1.tslxhfnlXWvumypxP8dGsISaldsFK8MJekAJIDuKuEGER8EcbN8hrroMSRiv09JM-&h=1080&w=1920&format=jpg';
$prefix = 'http://localhost:8080' . $_SERVER['REQUEST_URI'];



//resize function --------------------------------------------------------

function resize_image($file, $new_width, $folder, $file_name) {
        
  list($width, $height) = getimagesize($file);
  
  $coefficient = $new_width/$width;
  $new_height = $height * $coefficient;
  
  // Load
  $thumb = imagecreatetruecolor($new_width, $new_height);
  $source = imagecreatefromjpeg($file);
  
  // Resize
  imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  
  // Output 
  imagejpeg($thumb, $folder . $new_width . $file_name);
}

//check if the url has already been downloaded -------------------------
$resultat = $pdo->query("SELECT url FROM images WHERE url ='$url'");
$img = $resultat->fetch(PDO::FETCH_ASSOC);

//if already downloaded, display the right size
if ($img['url']) {
    $resultat = $pdo->query("SELECT $device FROM images WHERE url ='$url'");
    $path = $resultat->fetch(PDO::FETCH_ASSOC);
    var_dump($device)
    ?>
    <img src="<?php echo $prefix . $path[$device] ?>">
    <?php
}
//if the url is not already in the database
else {  
  define('LOCAL_IMG_DIR' , './images/'); 
  $online_images[] = array( 
    'online_image_url' => 'https://' . $url, 'local_img_name' => 'naruto.jpg'); 

  foreach ($online_images as $image_to_download) { 
    $image_online_url = file_get_contents($image_to_download['online_image_url']); 
    if ($image_online_url != '') { 
      echo '<br>Récupération OK : '.$image_to_download['online_image_url']; 
      if (file_put_contents(LOCAL_IMG_DIR.$image_to_download['local_img_name'], $image_online_url)){
        echo ' --> Ecriture OK : '.LOCAL_IMG_DIR.$image_to_download['local_img_name'];

        $file_name = 'naruto.jpg';
        $folder = 'images/';
        $image_path = $folder . $file_name;
        $mobile_width = 320;
        $tablet_width = 640;
        $desktop_width = 1366;
        
        resize_image($image_path, $mobile_width, $folder, $file_name);
        resize_image($image_path, $tablet_width, $folder, $file_name);
        resize_image($image_path, $desktop_width, $folder, $file_name);
        
        $path_mobile = $folder . $mobile_width . $file_name;
        $path_tablet = $folder . $tablet_width . $file_name;
        $path_desktop = $folder . $desktop_width . $file_name;
        
        $pdo-> exec("
        INSERT INTO images VALUES (
          '',
          '$url',
          '$path_mobile',
          '$path_tablet',
          '$path_desktop'
        )"
        );

        if ($device) {
          $resultat = $pdo->query("SELECT $device FROM images WHERE url ='$url'");
          $path = $resultat->fetch(PDO::FETCH_ASSOC);
          var_dump($resultat)
            ?>
            <img src="<?php echo $prefix . $path[$device] ?>">
            <?php
          }
      } else { 
        echo '<br/>Erreur d\'écriture vers : '.LOCAL_IMG_DIR.$image_to_download['local_img_name']; 
      } 
    } else { 
      echo '<br>Impossible de récupérer '.$image_to_download['online_image_url']; 
    } 
  }
}
?>
</body>
</html>

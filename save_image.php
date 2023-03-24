<?php
if (isset($_POST['image'])) {
  // Decode image data
  $data = base64_decode(str_replace('data:image/png;base64,', '', $_POST['image']));

  // Save image to file
  $filename = 'images/' . uniqid() . '.png';
  file_put_contents($filename, $data);

  // Return success response
  echo 'OK';
} else {
  // Return error response
  header('HTTP/1.1 400 Bad Request');
  echo 'Error: image data not found';
}
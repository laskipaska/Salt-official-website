<?php
$dir = 'images/';
$images = glob($dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

foreach ($images as $image) {
  echo '<img src="' . $image . '" alt="' . basename($image) . '">';
}
?>
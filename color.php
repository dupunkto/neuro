<?php
// Utitities for working with HEX-colors.

function lighten($hex, $percent) {
  $hex = str_replace('#', '', $hex);
  $r = hexdec(substr($hex, 0, 2));
  $g = hexdec(substr($hex, 2, 2));
  $b = hexdec(substr($hex, 4, 2));

  $r = min(255, round($r + (255 - $r) * $percent));
  $g = min(255, round($g + (255 - $g) * $percent));
  $b = min(255, round($b + (255 - $b) * $percent));

  return sprintf("#%02x%02x%02x", $r, $g, $b);
}

function darken($hex, $percent) {
  $hex = str_replace('#', '', $hex);
  $r = hexdec(substr($hex, 0, 2));
  $g = hexdec(substr($hex, 2, 2));
  $b = hexdec(substr($hex, 4, 2));

  $r = max(0, round($r - $r * $percent));
  $g = max(0, round($g - $g * $percent));
  $b = max(0, round($b - $b * $percent));

  return sprintf("#%02x%02x%02x", $r, $g, $b);
}

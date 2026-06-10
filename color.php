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

function contrast_color($hex, $light = "#ffffff", $dark = null) {
  // Relative luminance per WCAG, picks $light on dark backgrounds and a
  // near-black derived from $hex on light ones (so the dark text stays
  // visually anchored to the calendar's hue rather than pure black).
  $hex = str_replace('#', '', $hex);
  $r = hexdec(substr($hex, 0, 2)) / 255;
  $g = hexdec(substr($hex, 2, 2)) / 255;
  $b = hexdec(substr($hex, 4, 2)) / 255;

  $linearize = fn($c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
  $luminance = 0.2126 * $linearize($r) + 0.7152 * $linearize($g) + 0.0722 * $linearize($b);

  return $luminance < 0.4 ? $light : ($dark ?? darken("#$hex", 0.85));
}

<?php
// String utilities

function is_whitespace($c) {
  return in_array($c, array(" ", "\t", "\n", "\r", "\0", "\x0B"));
}

function is_email($str) {
  return substr_count($str, '@') == 1;
}

function is_url($str) {
  // In the context of HTML, paths starting with double slashes
  // are often treated as full-blown HTTP urls. (Where the double
  // slash indicated 'use current protocol'.)

  return str_starts_with($str, "http://")
    || str_starts_with($str, "https://")
    || str_starts_with($str, "//")
    || str_starts_with($str, "data:");
}

function ensure_prefix($str, $prefix) {
  return str_starts_with($str, $prefix) ? $str : $prefix. $str;
}

function strip_prefix($str, $prefix) {
  return replace_prefix($str, $prefix, "");
}

function replace_prefix($str, $old, $new) {
  if(str_starts_with($str, $old)) {
    return $new . substr($str, strlen($old));
  } else {
    return $str;
  }
}

function ensure_suffix($str, $suffix) {
  return str_ends_with($str, $suffix) ? $str : $str . $suffix;
}

function strip_suffix($str, $suffix) {
  return replace_suffix($str, $suffix, "");
}

function replace_suffix($str, $old, $new) {
  if(str_ends_with($str, $old)) {
    return substr($str, 0, strlen($str) - strlen($old)) . $new;
  } else {
    return $str;
  }
}

function is_nonempty_str($str) {
  return trim($str ?? "") !== "";
}

function str_contains_terms($haystack, $needles) {
  foreach($needles as $needle) {
    if(mb_stripos($haystack, $needle) === false) return false;
  }

  return true;
}

function str_implode($separator, $array) {
  $array = array_map('trim', $array);
  return implode($separator, array_filter($array, fn($p) => $p !== ""));
}

function str_explode($str) {
  return preg_split('/\s+/', trim($str), -1, PREG_SPLIT_NO_EMPTY);
}

function slugify($str, $length = null) {
  $str = strtr($str, UNICODE_TABLE);
  $str = preg_replace('~[^\pL\d.]+~u', '-', $str);
  $str = preg_replace('~[^-\w.]+~', '-', $str);
  $str = trim($str, '-');
  $str = preg_replace('~-+~', '-', $str);
  $str = strtolower(str);

  if(isset($length) and $length < strlen($str))
    $str = rtrim(substr($str, 0, $length), '-');

  return $str;
}

function extract_email($email) {
  preg_match('/<([^<>]+)>/', $email, $matches);
  return $matches[1] ?? null;
}

function extract_match($str, $pattern, $group = 1) {
  $acc = [];
  preg_match_all($pattern, $str, $acc);
  return $group == false ? $acc : $acc[$group];
}

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

function str_normalize($str) {
  $str = mb_strtolower($str);
  $str = \Normalizer::normalize($str, \Normalizer::FORM_D);
  return preg_replace('/\p{Mn}+/u', '', $str);
}

function str_contains_term($haystack, $needle) {
  return str_contains(str_normalize($haystack), str_normalize($needle));
}

function str_contains_terms($haystack, $needles) {
  $haystack = str_normalize($haystack);

  foreach($needles as $needle) {
    if(!str_contains($haystack, str_normalize($needle))) return false;
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
  $str = strtolower($str);

  if(isset($length) and $length < strlen($str))
    $str = rtrim(substr($str, 0, $length), '-');

  return $str;
}

function extract_url($str, $all = false) {
  $matches = extract_match($str, "=https?://[][[:alnum:]._~:/?#@!$&'()*+,;%-]+=", group: 0);

  return $all ? $matches : @$matches[0];
}

function extract_email($str, $all = false) {
  $matches = extract_match($str, '/[[:alnum:]+._-]*@[[:alnum:]+._-]*/', group: 0);

  return $all ? $matches : @$matches[0];
}

function extract_ip($str, $all = false) {
  $octet = '(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';
  $ipv4 = "$octet(?:\\.$octet){3}";
  $hex = '[0-9A-Fa-f]{1,4}';
  $ipv6 = '(?:'
    . "(?:$hex:){7}(?:$hex|:)"
    . "|(?:$hex:){6}(?::$hex|$ipv4|:)"
    . "|(?:$hex:){5}(?:(?::$hex){1,2}|:$ipv4|:)"
    . "|(?:$hex:){4}(?:(?::$hex){1,3}|(?::$hex)?:$ipv4|:)"
    . "|(?:$hex:){3}(?:(?::$hex){1,4}|(?::$hex){0,2}:$ipv4|:)"
    . "|(?:$hex:){2}(?:(?::$hex){1,5}|(?::$hex){0,3}:$ipv4|:)"
    . "|(?:$hex:)(?:(?::$hex){1,6}|(?::$hex){0,4}:$ipv4|:)"
    . "|:(?:(?::$hex){1,7}|(?::$hex){0,5}:$ipv4|:)"
    . ')(?:%[^\s]+)?';

  $matches = extract_match($str, "~$ipv4|$ipv6~", group: 0);

  return $all ? $matches : @$matches[0];
}

function extract_match($str, $pattern, $group = 1) {
  $acc = [];
  preg_match_all($pattern, $str, $acc);
  return $group === false ? $acc : $acc[$group];
}

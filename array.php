<?php
// Array utilities.

function assoc($term) {
  if(is_object($term)) $term = get_object_vars($term);
  if(is_array($term)) return array_map(__FUNCTION__, $term);
  return $term;
}

function is_nonempty_list($value) {
  return is_array($value) && array_is_list($value) && !empty($value);
}

function is_map($value) {
  return is_array($value) && (empty($value) || !array_is_list($value));
}

function array_require_string($array, $key) {
  if(!array_key_exists($key, $array))
    throw new InvalidArgumentException("Missing required key '$key'.");
  if(!is_string($array[$key]))
    throw new InvalidArgumentException("Array key '$key' must be a string.");
  return $array[$key];
}

function array_get_string($array, $key, $default = null) {
  if(!array_key_exists($key, $array)) return $default;
  if(!is_string($array[$key]))
    throw new InvalidArgumentException("Array key '$key' must be a string.");
  return $array[$key];
}

function array_require_int($array, $key) {
  if(!array_key_exists($key, $array))
    throw new InvalidArgumentException("Missing required key '$key'.");
  if(!is_int($array[$key]))
    throw new InvalidArgumentException("Array key '$key' must be an integer.");
  return $array[$key];
}

function array_get_int($array, $key, $default = null) {
  if(!array_key_exists($key, $array)) return $default;
  if(!is_int($array[$key]))
    throw new InvalidArgumentException("Array key '$key' must be an integer.");
  return $array[$key];
}

function pluck($items, $key) {
  return array_values(array_map(
    fn($item) => is_array($item) ? $item[$key] : $item,
    $items
  ));
}

function arr_explode($string, $separator = ",") {
  return array_filter(array_map('trim', explode($separator, $string)));
}

function arr_implode($array, $separator = ",") {
  $array = array_map('trim', $array);
  return implode($separator, array_filter($array, fn($p) => $p !== ""));
}

function zip($separator, $array) {
  $keys = array_keys($array);
  $values = array_values($array);
  
  return array_map(function($key, $value) use ($separator) {
    return $key . $separator . $value;
  }, $keys, $values);
}

function flatten($array) {
  return array_reduce($array, function($acc, $item) {
    return array_merge($acc, is_array($item) ? flatten($item) : [$item]);
  }, []);
}

function count_by($array, $key) {
  $keys = array_column($array, $key);
  $array = array_reduce($keys, function($acc, $key) {
    if(isset($acc[$key])) $acc[$key]++;
    else $acc[$key] = 1;

    return $acc;
  }, []);

  arsort($array); // Ew, in-place mutation.
  return $array;
}

function group_by($items, $prefix) {
  $grouped = [];
  
  foreach($items as $item) {
    $id = $item[$prefix . "_id"];

    if(!isset($grouped[$id])) {
      $grouped[$id] = array_merge(
          unprefix_keys($item, $prefix),
          ['items' => []]
      );
    }
    
    $grouped[$id]['items'][] = $item;
  }

  return $grouped;
}

function collect_by($items, $prefix, $as) {
  $grouped = [];
  $prefix_str = $prefix . '_';

  foreach($items as $item) {
    $id = $item['id'];

    if(!isset($grouped[$id])) {
      $base = array_filter($item, fn($k) => !str_starts_with($k, $prefix_str), ARRAY_FILTER_USE_KEY);
      $grouped[$id] = array_merge($base, [$as => []]);
    }

    $nested = unprefix_keys($item, $prefix);
    if(array_filter($nested, fn($v) => $v !== null)) {
      $grouped[$id][$as][] = $nested;
    }
  }

  return array_values($grouped);
}

function find_by($haystack, $key, $value) {
  foreach($haystack as $item)
    if(@$item[$key] === $value) return $item;

  return null;
}

function take($array, $amount) {
  return array_slice($array, 0, $amount);
}

function insert($array, $position, $item) {
  array_splice($array, $position, 0, [$item]);
  return $array;
}

function sorted($array, $compare) {
  usort($array, $compare);
  return $array;
}

function map($array, $key_k, $value_k) {
  return array_reduce($array, function($acc, $item) {
    $acc[$item[$key_k]] = $item[$value_k];
    return $acc;
  }, []);
}

function repeat($keys, $value) {
  return array_fill_keys($keys, $value);
}

function unfold($source, $prefix, $required = null) {
  $columns = unprefix_keys($source, $prefix);
  $count = max([0, ...array_map(fn($values) => count((array)$values), $columns)]);

  $rows = [];
  for($i = 0; $i < $count; $i++) {
    $row = [];
    foreach($columns as $column => $values) {
      $values = (array)$values;
      $value = @$values[$i] === null ? null : trim((string)$values[$i]);
      $row[$column] = $value === "" ? null : $value;
    }

    if(array_filter($row, fn($value) => $value !== null)) $rows[] = $row;
  }

  return $rows;
}

function prefix_keys($array, $prefix) {
  return array_combine(
    array_map(fn($k) => "$prefix$k", array_keys($array)),
    array_values($array)
  );
}

function unprefix_keys($array, $prefix) {
  $filtered = [];
  $prefix = $prefix."_";
  $len = strlen($prefix);

  foreach($array as $key => $value) {
    if(strpos($key, $prefix) === 0) {
      $filtered[substr($key, $len)] = $value; 
    }
  }

  return $filtered;
}

function drop_empty($array) {
  return array_filter($array, fn($value) => !in_array($value, ["", null, false]));
}

function deep_contains($haystack, $needle) {
  return $needle and count(array_filter($haystack, fn($candidate) => strpos($needle, $candidate) != false)) > 0;
}

function has_exact_keys($value, $keys) {
  if(!is_array($value)) return false;
  $actual = array_keys($value);
  sort($actual);
  sort($keys);
  return $actual == $keys;
}

function has_duplicate_keys($array) {
  return count(array_unique($array)) != count($array);
}

<?php
// Array utilities.

function assoc($term) {
  if (is_object($term)) $term = get_object_vars($term);
  if (is_array($term)) return array_map(__FUNCTION__, $term);
  return $term;
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
    if (isset($acc[$key])) $acc[$key]++;
    else $acc[$key] = 1;

    return $acc;
  }, []);

  arsort($array); // Ew, in-place mutation.
  return $array;
}

function group_by($items, $prefix) {
  $grouped = [];
  
  foreach ($items as $item) {
    $id = $item[$prefix . "_id"];

    if (!isset($grouped[$id])) {
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

  foreach ($items as $item) {
    $id = $item['id'];

    if (!isset($grouped[$id])) {
      $base = array_filter($item, fn($k) => !str_starts_with($k, $prefix_str), ARRAY_FILTER_USE_KEY);
      $grouped[$id] = array_merge($base, [$as => []]);
    }

    $nested = unprefix_keys($item, $prefix);
    if (array_filter($nested, fn($v) => $v !== null)) {
      $grouped[$id][$as][] = $nested;
    }
  }

  return array_values($grouped);
}

function find_by($haystack, $key, $value) {
  foreach ($haystack as $item)
    if (@$item[$key] === $value) return $item;

  return null;
}

function take($array, $amount) {
  return array_slice($array, 0, $amount);
}

function map($array, $key_k, $value_k) {
  return array_reduce($array, function($acc, $item) {
    $acc[$item[$key_k]] = $item[$value_k];
    return $acc;
  }, []);
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

  foreach ($array as $key => $value) {
    if (strpos($key, $prefix) === 0) {
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

function allset($array, $keys) {
  foreach($keys as $key) if(!isset($array[$key])) return false;
  return true;
}
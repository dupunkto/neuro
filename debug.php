<?php
// Elixir + Rust fanboy :>

function dbg($thing) {
  echo "[dbg] ";
  var_dump($thing);
  return $thing;
}

function todo($msg) {
  die("TODO: $msg");
}

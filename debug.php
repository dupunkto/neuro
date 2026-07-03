<?php
// Elixir + Rust fanboy :>

function dbg($thing) {
  echo "[dbg] ";
  var_export($thing);
  echo "<br>";
  return $thing;
}

function todo($msg) {
  die("TODO: $msg");
}

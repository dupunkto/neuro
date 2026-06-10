<?php
// Lightweight logger. For now everything goes through PHP's error_log; later
// hook this into a Discord webhook (and probably split warn/error/info into
// separate severities so the webhook only fires on warn+).

namespace logger;

function info($message)  { write('info',  $message); }
function warn($message)  { write('warn',  $message); }
function error($message) { write('error', $message); }

function write($level, $message) {
  error_log("[$level] $message");
}

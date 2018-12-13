<?php
function mysqli_result($result, $number, $field = 0) {
  mysqli_data_seek($result, $number);
  $type = is_numeric($field) ? MYSQLI_NUM : MYSQLI_ASSOC;
  $out = mysqli_fetch_array($result, $type);
  if ($out === NULL || $out === FALSE || !isset($out[$field])) {
    return FALSE;
  }
  return $out[$field];
  }
?>
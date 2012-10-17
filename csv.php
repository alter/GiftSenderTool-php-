<?php
$filename="promo-codes.csv";
$handler = fopen($filename, 'w+');
if (isset($_POST["fields"])) {
  $fieldArray = $_POST['fields'];
  foreach ($fieldArray as &$field) {
    fwrite($handler, $field);
    fwrite($handler, "\n");
  }
  fclose($handler);
  unset($field);
  header('Content-type: application/csv');
  header('Content-Disposition: attachment;filename="'.$filename.'"');

  readfile($filename);
  unlink($filename);
}
?>

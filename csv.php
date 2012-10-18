<?php
if(isset($_POST["fields"])) {
  $file="/tmp/promo-codes.csv";
  $handler = fopen($file, 'w+');
  $fieldArray = $_POST['fields'];
  foreach ($fieldArray as &$field) {
    fwrite($handler, "$field\n");
  }
  fclose($handler);
  if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: 8bit');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
    }  
}
?>

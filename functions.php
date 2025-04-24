<?php
define("MB", 1048576); // 1MB = 1024 * 1024 bytes

function filterRequest($requestname)
{
   return htmlspecialchars(strip_tags($_POST[$requestname] ?? ''));
}

function getAllData($table, $where = null, $values = null, $json = true)
{
   global $con;
   $data = array();
   if ($where == null) {
      $stmt = $con->prepare("SELECT  * FROM $table   ");
   } else {
      $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
   }
   $stmt->execute($values);
   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
   $count  = $stmt->rowCount();
   if ($json == true) {
      if ($count > 0) {
         echo json_encode(array("status" => "success", "data" => $data));
      } else {
         echo json_encode(array("status" => "failure"));
      }
      return $count;
   } else {
      if ($count > 0) {
         return $data;
      } else {
         return json_encode(array("status" => "failure"));
      }
   }
}

function getData($table, $where = null, $values = null)
{
   global $con;
   $data = array();
   $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
   $stmt->execute($values);
   $data = $stmt->fetch(PDO::FETCH_ASSOC);
   $count  = $stmt->rowCount();
   if ($count > 0) {
      echo json_encode(array("status" => "success", "data" => $data));
   } else {
      echo json_encode(array("status" => "failure"));
   }
   return $count;
}

function insertData($table, $data, $json = true)
{
   global $con;
   foreach ($data as $field => $v)
      $ins[] = ':' . $field;
   $ins = implode(',', $ins);
   $fields = implode(',', array_keys($data));
   $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

   $stmt = $con->prepare($sql);
   foreach ($data as $f => $v) {
      $stmt->bindValue(':' . $f, $v);
   }
   $stmt->execute();
   $count = $stmt->rowCount();
   if ($json == true) {
      if ($count > 0) {
         echo json_encode(array("status" => "success"));
      } else {
         echo json_encode(array("status" => "failure"));
      }
   }
   return $count;
}

function updateData($table, $data, $where, $json = true)
{
   global $con;
   $cols = array();
   $vals = array();

   foreach ($data as $key => $val) {
      $vals[] = "$val";
      $cols[] = "`$key` =  ? ";
   }
   $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

   $stmt = $con->prepare($sql);
   $stmt->execute($vals);
   $count = $stmt->rowCount();
   if ($json == true) {
      if ($count > 0) {
         echo json_encode(array("status" => "success"));
      } else {
         echo json_encode(array("status" => "failure"));
      }
   }
   return $count;
}

function deleteData($table, $where, $json = true)
{
   global $con;
   $stmt = $con->prepare("DELETE FROM $table WHERE $where");
   $stmt->execute();
   $count = $stmt->rowCount();
   if ($json == true) {
      if ($count > 0) {
         echo json_encode(array("status" => "success"));
      } else {
         echo json_encode(array("status" => "failure"));
      }
   }
   return $count;
}

function imageUpload($imageRequest)
{
   global $msgError;
   $msgError = [];
   $imagename = rand(1000, 1000) . $_FILES[$imageRequest]['name'];
   $imagetmp  = $_FILES[$imageRequest]['tmp_name'];
   $imagesize = $_FILES[$imageRequest]['size'] ?? 0;
   $allowExt  = array("jpg", "jpeg", "png", "gif");
   $strtorray = explode(".", $imagename);
   $ext       = end($strtorray);
   $ext       = strtolower($ext);

   if (!empty($imagename) && !in_array($ext, $allowExt)) {
      $msgError[] = "Invalid file type: only jpg, jpeg, png, and gif are allowed";
   }
   if ($imagesize > 10 * MB) {
      $msgError[] = "File size is too large: maximum 10MB allowed";
   }

   if (empty($msgError)) {
      if (!is_dir("upload")) {
         mkdir("upload", 0777, true);
      }
      if (move_uploaded_file($imagetmp, "../upload/" . $imagename)) {
         return $imagename;
      } else {
         return "fail";
      }
   } else {
      return "fail";
   }
}

function deleteFile($dir, $imagename)
{
   $filepath = $dir . "/" . $imagename;
   // Make sure it's a file and not a directory
   if (!empty($imagename) && file_exists($filepath) && is_file($filepath)) {
      unlink($filepath);
   }
}


function checkAuthenticate()
{
   if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {

      if ($_SERVER['PHP_AUTH_USER'] != "abdullah" ||  $_SERVER['PHP_AUTH_PW'] != "abdullah2241") {
         header('WWW-Authenticate: Basic realm="My Realm"');
         header('HTTP/1.0 401 Unauthorized');
         echo 'Page Not Found';
         exit;
      }
   } else {
      exit;
   }
}

function printFailure($msg = "null")
{
   echo json_encode(array("status" => "failure", "message" => $msg));
}
function printSuccess($msg = "null")
{
   echo json_encode(array("status" => "success", "message" => $msg));
}

function result($count)
{
   if ($count > 0) {
      printSuccess();
   } else {
      printFailure();
   }
}

function sendEmail($to, $title, $body)
{
   $header = "From: support@abdullah.com " . "\n" . "CC: waeleagle1243@gmail.com";
   mail($to, $title, $body, $header);
}

<?php
if (!isset($_REQUEST['card']) || empty($_REQUEST['card'])) {
  die(json_encode([
    "success" => false,
    "message" => "CARD number is required."
  ]));
}

$card = $_REQUEST['card'];


//Fix Cors

if (isset($_SERVER['HTTP_ORIGIN'])) {
  // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
  // you want to allow, and if so:
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    // may also be using PUT, PATCH, HEAD etc
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

  exit(0);
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://rapidpass.com.bd/bn/index.php/welcome/searchRegistraionInfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "search=" . $card);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$headers = array();
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
$headers[] = 'Accept-Language: en-BD,en-US;q=0.9,en-GB;q=0.8,en;q=0.7,bn;q=0.6';
$headers[] = 'Cache-Control: max-age=0';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'Cookie: PHPSESSID=fkqrn1d733metse4kkrg22u33m';
$headers[] = 'Origin: https://rapidpass.com.bd';
$headers[] = 'Referer: https://rapidpass.com.bd/bn/index.php/welcome/afc_topup';
$headers[] = 'Sec-Fetch-Dest: document';
$headers[] = 'Sec-Fetch-Mode: navigate';
$headers[] = 'Sec-Fetch-Site: same-origin';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36';
$headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"124\", \"Google Chrome\";v=\"124\", \"Not-A.Brand\";v=\"99\"';
$headers[] = 'Sec-Ch-Ua-Mobile: ?1';
$headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
  echo 'Error:' . curl_error($ch);
}
curl_close($ch);

// die($result);

$result = trim(str_replace("<!-- ", "", $result));
$result = trim(str_replace("--> ", "", $result));

$src = new DOMDocument('1.0', 'utf-8');
$src->formatOutput = true;
$src->preserveWhiteSpace = false;
@$src->loadHTML($result);
$xpath = new DOMXPath($src);
$values = $xpath->query('//td[ contains (@class, "text-right") ]');
$info = [];
foreach ($values as $value) {
  // echo $value->nodeValue;
  if ($value->nodeValue !== "") {
    array_push($info, $value->nodeValue);
  }
}

$dataset = [
  "ID" => preg_replace('/\s+/', '', $info[1]),
  "Name" => $info[0],
  "Balance" => preg_replace('/\s+/', '', $info[2]),
  "Status" => preg_replace('/\s+/', '', $info[3]),
];

echo json_encode($dataset);

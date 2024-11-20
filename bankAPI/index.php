<?php
require_once('Route.php');
require_once('model/Account.php');
//model użytkownika
require_once('model/User.php');
//model tokena
require_once('model/Token.php');
require_once('model/Transfer.php');
$db = new mysqli('localhost', 'root', '', 'bankAPI');
$db->set_charset('utf8');
use Steampixel\Route;
use BankAPI\Account;
Route::add('/', function() {
  echo 'Hello world!';
});
Route::add('/login', function() use($db) {
  $data = file_get_contents('php://input');
  $data = json_decode($data, true);
  $ip = $_SERVER['REMOTE_ADDR'];
  try {
    $id = User::login($data['login'], $data['password'], $db);
    $token = Token::new($ip, $id, $db);
    header('Content-Type: application/json');
    echo json_encode(['token' => $token]);
  } catch (Exception $e) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Invalid login or password']);
    return;
  }
}, 'post');
Route::add('/account/details', function() use($db) {
    $data = file_get_contents('php://input');
    $dataArray = json_decode($data, true);
    $token = $dataArray['token'];
    if(!Token::check($token, $_SERVER['REMOTE_ADDR'], $db)) {
        header('HTTP/1.1 401 Unauthorized');
        return json_encode(['error' => 'Invalid token']);
    }
    $userId = Token::getUserId($token, $db);
    $accountNo = Account::getAccountNo($userId, $db);
    $account = Account::getAccount($accountNo, $db);
    header('Content-Type: application/json');
    return json_encode($account->getArray());
}, 'post');
Route::add('/account/([0-9]*)', function($accountNo) use($db) {
    $account = Account::getAccount($accountNo, $db);
    header('Content-Type: application/json');
    return json_encode($account->getArray());
});

Route::add('/transfer/new', function() use($db) {
    $data = file_get_contents('php://input');
    $dataArray = json_decode($data, true);
    $token = $dataArray['token'];
    if(!Token::check($token, $_SERVER['REMOTE_ADDR'], $db)) {
        header('HTTP/1.1 401 Unauthorized');
        return json_encode(['error' => 'Invalid token']);
    }
    $userId = Token::getUserId($token, $db);
    $source = Account::getAccountNo($userId, $db);
    $target = $dataArray['target'];
    $amount = $dataArray['amount'];
    Transfer::new($source, $target, $amount, $db);
    header('Status: 200');
    return json_encode(['status' => 'OK']);
}, 'post');
Route::run('/bankAPI');
$db->close();
?>
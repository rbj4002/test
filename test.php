<?php
// Set PHP to display errors on the screen for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// The endpoint we want to test
$url = 'http://185.218.125.242:8080/api/receive';

echo "<h1>Testing Connection...</h1>";
echo "<p>Attempting to connect to: <strong>" . $url . "</strong></p>";
echo "<hr>";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout after 10 seconds

$response = curl_exec($ch);

// This is where we print the error to the screen
if (curl_errno($ch)) {
    $error_message = curl_error($ch);
    echo "<h2>RESULT: <font color='red'>CONNECTION FAILED</font></h2>";
    echo "<h3>Error Message:</h3>";
    echo "<pre style='background:#ffebeb; border:1px solid red; padding:10px;'>" . $error_message . "</pre>";
} else {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "<h2>RESULT: <font color='green'>Connection Successful!</font></h2>";
    echo "<h3>HTTP Status Code:</h3>";
    echo "<pre style='background:#e6ffed; border:1px solid green; padding:10px;'>" . $http_code . "</pre>";
}

curl_close($ch);
?>

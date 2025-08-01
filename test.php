<?php
// Set headers to display errors and ensure the output is treated as plain text
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "--- Starting Full Test to Flask API ---\n\n";

// --- CONFIGURATION ---
// 1. IMPORTANT: Replace this with the FULL, PUBLIC URL of your Flask app's endpoint
$flask_api_url = 'http://185.218.125.242:8080/api/receive'; // Example: https://myapp.com/api/receive

// 2. The EXACT same secret API Key as in your Python script
$secret_api_key = 'a-very-long-and-secret-password-12345';

// 3. The secret key for encrypting the password (must match your Flask app)
$encryption_key_hex = '3f5a8e72d4b9c1f3a9e7d3c2b1f0a9e8c5d6f7e8a9b0c1d2e3f4a5b6c7d8e9f0';
$encryption_key_bin = hex2bin($encryption_key_hex);

// --- DATA PREPARATION ---

// Function to encrypt data just like your Flask app expects
function encrypt_for_flask($plaintext, $key) {
    // AES-256-CBC uses a 16-byte IV (Initialization Vector)
    $iv = openssl_random_pseudo_bytes(16); 
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    // Combine IV and ciphertext, then Base64 encode for safe transport
    return base64_encode($iv . $ciphertext);
}

// Sample data to send
$password_to_encrypt = 'a-test-password-from-php';
$encrypted_password = encrypt_for_flask($password_to_encrypt, $encryption_key_bin);

$data_to_send = [
    'name' => 'PHP Full Test User',
    'email' => 'fulltest_' . time() . '@example.com', // Unique email every time
    'duration' => 30,
    'encrypted_password' => $encrypted_password
];

// Convert the PHP array into a JSON string
$json_payload = json_encode($data_to_send);

echo "Payload to be sent:\n";
echo $json_payload . "\n\n";

// --- cURL REQUEST ---

// 1. Initialize cURL
$ch = curl_init();

// 2. Set cURL options
curl_setopt($ch, CURLOPT_URL, $flask_api_url); // Set the target URL
curl_setopt($ch, CURLOPT_POST, true); // Specify this is a POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload); // Attach the JSON data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string instead of printing it
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout for connection
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout for the entire request

// 3. Set the required HTTP headers
// Your Flask app requires 'Content-Type: application/json' and the API key
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $secret_api_key
]);

// 4. Execute the request
echo "--- Sending request to Flask... ---\n\n";
$response = curl_exec($ch);

// 5. Check for errors
if (curl_errno($ch)) {
    // If cURL itself fails (e.g., can't connect, URL is wrong, timeout)
    echo "cURL Error:\n";
    echo '#' . curl_errno($ch) . ' - ' . curl_error($ch) . "\n";
} else {
    // If the request was successful, print the response from the Flask API
    echo "Response from Flask API:\n";
    
    // Try to decode the JSON response for pretty printing
    $decoded_response = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        print_r($decoded_response);
    } else {
        // If the response is not valid JSON, print it as raw text
        echo $response;
    }
}

// 6. Close the cURL session
curl_close($ch);

echo "\n\n--- Test Finished ---";
?>

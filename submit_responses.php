<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $responses = json_decode(file_get_contents('php://input'), true);

    // Google Sheets URL (replace with your actual Google Sheets Web App URL)
    $googleSheetsUrl = 'https://script.google.com/macros/s/AKfycbyLViqjs9Z5uPMx6pMMWt8K1J-7B0ifg51AJfdlS44lZA9U3OqiaWbBPeeVNuH08ulp/exec';

    // Prepare the data to be sent to Google Sheets
    $postData = json_encode($responses);

    // Initialize cURL session
    $ch = curl_init($googleSheetsUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

    // Execute cURL request and get the response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    // Close cURL session
    curl_close($ch);

    // Check the response status
    if ($httpCode == 200 && $response) {
        echo json_encode(['status' => 'success', 'message' => 'Your responses have been submitted successfully!']);
    } else {
        $errorMessage = 'There was an error submitting your responses. Please try again.';
        if ($curlError) {
            $errorMessage .= ' cURL error: ' . $curlError;
        } else {
            $errorMessage .= ' HTTP status code: ' . $httpCode . '. Response: ' . $response;
        }
        echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>

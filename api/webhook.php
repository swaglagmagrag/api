<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data && isset($data['webhook']) && isset($data['message'])) {
        $webhookUrl = $data['webhook'];
        $message = $data['message'];

        $payload = json_encode(array('content' => $message));

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 204) {
            $responseData = array('status' => 'success');
        } else {
            $responseData = array('status' => 'error');
        }

        curl_close($ch);
    } else {
        http_response_code(400);
        $responseData = array('status' => 'invalid_request');
    }

    // Set the correct content type in the response
    header('Content-Type: application/json');
    echo json_encode($responseData);
} else {
    http_response_code(405);
    echo json_encode(array('status' => 'method_not_allowed'));
}
?>
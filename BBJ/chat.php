<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_message = $_POST['message'] ?? '';

    if (!$user_message) {
        echo "Please enter a message.";
        exit();
    }

    $api_key = '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';

    $prompt = "You are a helpful tutor bot.\nUser: " . $user_message . "\nAI:";

    $data = [
        "inputs" => $prompt,
        "parameters" => [
            "max_new_tokens" => 200,
            "temperature" => 0.7,
            "return_full_text" => false
        ]
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => [
                "Content-Type: application/json",
                "Authorization: Bearer $api_key"
            ],
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents("https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.1", false, $context);
    $responseData = json_decode($response, true);

    if (isset($responseData[0]['generated_text'])) {
        echo trim($responseData[0]['generated_text']);
    } else {
        echo "Sorry, I couldn't get a response.";
    }
}
?>

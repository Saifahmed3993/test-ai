<?php
// Test script for both OpenAI and Preplexity AI API integration
require_once 'backend/api/config.php';

echo "Testing AI API Integration (OpenAI + Preplexity AI + DeepSeek)...\n\n";

// Test data
$testMessage = "How can I improve my resume for a software developer position?";

echo "Test Message: " . $testMessage . "\n\n";

// API configurations
$apis = [
    [
        'name' => 'OpenAI',
        'apiKey' => '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',
        'apiUrl' => 'https://api.openai.com/v1/chat/completions',
        'model' => 'gpt-3.5-turbo',
        'max_tokens' => 1000,
        'temperature' => 0.7
    ],
    [
        'name' => 'Preplexity AI',
        'apiKey' => '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',
        'apiUrl' => 'https://api.perplexity.ai/chat/completions',
        'model' => 'llama-3.1-sonar-small-128k',
        'max_tokens' => 1000,
        'temperature' => 0.7,
        'top_p' => 0.9
    ],
    [
        'name' => 'DeepSeek',
        'apiKey' => 'sk-16331491db5a4faa97c50c5a29394f0a',
        'apiUrl' => 'https://api.deepseek.com/v1/chat/completions',
        'model' => 'deepseek-chat',
        'max_tokens' => 1000,
        'temperature' => 0.7
    ],
    [
        'name' => 'Gemini',
        'apiKey' => 'AIzaSyBRuEcyfuVN-qJSEpYdmddPVJJ0_mfGO4o',
        'apiUrl' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
        'model' => 'gemini-2.0-flash',
        'max_tokens' => 1000,
        'temperature' => 0.7
    ]
];

// Prepare the prompt for career guidance
$systemPrompt = "You are a professional career guidance counselor and AI assistant. Your role is to help users with career-related questions, job search advice, skill development, interview preparation, resume tips, and career planning. Provide helpful, practical, and encouraging advice. Keep responses conversational but professional. If asked about non-career topics, politely redirect to career-related subjects. Always be supportive and provide actionable advice when possible.";

// Test each API
foreach ($apis as $api) {
    echo "Testing " . $api['name'] . "...\n";
    
    try {
        // Prepare the request data based on API type
        if ($api['name'] === 'Gemini') {
            // Gemini uses a different request format
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $systemPrompt . "\n\nUser: " . $testMessage . "\n\nAssistant:"
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => $api['max_tokens'],
                    'temperature' => $api['temperature']
                ]
            ];
            
            // Set headers for Gemini
            $headers = [
                'X-goog-api-key: ' . $api['apiKey'],
                'Content-Type: application/json',
                'Accept: application/json'
            ];
        } else {
            // Standard OpenAI-compatible format
            $requestData = [
                'model' => $api['model'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $testMessage
                    ]
                ],
                'max_tokens' => $api['max_tokens'],
                'temperature' => $api['temperature']
            ];
            
            // Add top_p if it exists (for Preplexity)
            if (isset($api['top_p'])) {
                $requestData['top_p'] = $api['top_p'];
            }
            
            // Set headers for other APIs
            $headers = [
                'Authorization: Bearer ' . $api['apiKey'],
                'Content-Type: application/json',
                'Accept: application/json'
            ];
        }

        // Initialize cURL
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $api['apiUrl'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'CareerGuidanceApp/1.0'
        ]);
        
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        echo "HTTP Status Code: " . $httpCode . "\n";
        
        if ($error) {
            echo "❌ " . $api['name'] . " cURL Error: " . $error . "\n\n";
            continue;
        }
        
        if ($httpCode !== 200) {
            echo "❌ " . $api['name'] . " API Error: HTTP " . $httpCode . "\n";
            echo "Response: " . $response . "\n\n";
            continue;
        }
        
        // Parse the response
        $responseData = json_decode($response, true);
        
        if (!$responseData) {
            echo "❌ " . $api['name'] . " Error: Invalid JSON response\n";
            echo "Raw response: " . $response . "\n\n";
            continue;
        }
        
        // Extract the AI response based on API type
        if ($api['name'] === 'Gemini') {
            // Gemini response format
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
            } else {
                echo "❌ " . $api['name'] . " Error: Unexpected Gemini response format\n";
                echo "Response data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
                continue;
            }
        } else {
            // Standard OpenAI-compatible response format
            if (isset($responseData['choices'][0]['message']['content'])) {
                $aiResponse = $responseData['choices'][0]['message']['content'];
            } else {
                echo "❌ " . $api['name'] . " Error: Unexpected response format\n";
                echo "Response data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
                continue;
            }
        }
        
        echo "✅ " . $api['name'] . " API Test Successful!\n\n";
        echo "AI Response:\n";
        echo "----------------------------------------\n";
        echo $aiResponse . "\n";
        echo "----------------------------------------\n\n";
        
        echo "Response length: " . strlen($aiResponse) . " characters\n";
        if ($api['name'] === 'Gemini') {
            echo "Model used: " . $api['model'] . "\n";
        } else {
            echo "Model used: " . $responseData['model'] . "\n";
        }
        if (isset($responseData['usage'])) {
            echo "Usage: " . json_encode($responseData['usage']) . "\n";
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "❌ " . $api['name'] . " Error: " . $e->getMessage() . "\n\n";
        continue;
    }
}

echo "\n🎉 AI API integration testing completed!\n";
echo "The system will automatically use whichever API is available.\n";
?>

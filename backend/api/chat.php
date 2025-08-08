<?php
require_once 'config.php';

// Set headers for CORS and JSON
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['message'])) {
        throw new Exception('Message is required');
    }
    
    $userMessage = trim($input['message']);
    
    if (empty($userMessage)) {
        throw new Exception('Message cannot be empty');
    }
    
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
    $systemPrompt = "You are a friendly, warm, and knowledgeable career guidance counselor named 'Career AI Assistant'. You should:

1. **Be conversational and natural** - Talk like you're chatting with a friend, not giving a formal presentation
2. **Use the user's name** - If they've mentioned their name, use it in your responses to make it personal
3. **Show empathy and understanding** - Acknowledge their feelings and concerns
4. **Be encouraging and supportive** - Always be positive and motivating
5. **Use casual language** - Avoid overly formal language, use contractions (I'm, you're, we'll)
6. **Ask follow-up questions** - Engage them in conversation
7. **Share personal insights** - Make it feel like you're sharing from experience
8. **Use emojis occasionally** - But not too many, just to make it friendly
9. **Be specific and actionable** - Give practical advice they can actually use
10. **Remember context** - Reference previous parts of the conversation

Your role is to help users with career-related questions, job search advice, skill development, interview preparation, resume tips, and career planning. Keep responses conversational but professional. If asked about non-career topics, politely redirect to career-related subjects. Always be supportive and provide actionable advice when possible.";
    
    // Try each API until one works
    $lastError = null;
    
    foreach ($apis as $api) {
        try {
            // Prepare the request data based on API type
            if ($api['name'] === 'Gemini') {
                // Gemini uses a different request format
                $requestData = [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $systemPrompt . "\n\nUser: " . $userMessage . "\n\nAssistant:"
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
                            'content' => $userMessage
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
            
            // Handle cURL errors
            if ($error) {
                throw new Exception($api['name'] . ' network error: ' . $error);
            }
            
            // Handle HTTP errors
            if ($httpCode !== 200) {
                // Special handling for DeepSeek insufficient balance
                if ($api['name'] === 'DeepSeek' && $httpCode === 402) {
                    throw new Exception($api['name'] . ' insufficient balance - skipping to next API');
                }
                throw new Exception($api['name'] . ' API request failed with status code: ' . $httpCode);
            }
            
            // Parse the response
            $responseData = json_decode($response, true);
            
            if (!$responseData) {
                throw new Exception('Invalid response from ' . $api['name']);
            }
            
            // Extract the AI response based on API type
            if ($api['name'] === 'Gemini') {
                // Gemini response format
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
                } else {
                    throw new Exception('Unexpected Gemini response format');
                }
            } else {
                // Standard OpenAI-compatible response format
                if (isset($responseData['choices'][0]['message']['content'])) {
                    $aiResponse = $responseData['choices'][0]['message']['content'];
                } else {
                    throw new Exception('Unexpected response format from ' . $api['name']);
                }
            }
            
            // Clean up the response
            $aiResponse = trim($aiResponse);
            
            // Ensure the response is not empty
            if (empty($aiResponse)) {
                throw new Exception('Empty response from ' . $api['name']);
            }
            
            // Return success response
            echo json_encode([
                'status' => 'success',
                'message' => $aiResponse,
                'timestamp' => date('Y-m-d H:i:s'),
                'api_used' => $api['name']
            ]);
            return; // Exit successfully
            
        } catch (Exception $e) {
            $lastError = $e;
            error_log('Chat API Error (' . $api['name'] . '): ' . $e->getMessage());
            continue; // Try next API
        }
    }
    
    // If we get here, all APIs failed
    throw new Exception('All AI services are currently unavailable. Last error: ' . $lastError->getMessage());
    
} catch (Exception $e) {
    // Log error for debugging (in production, you might want to log to a file)
    error_log('Chat API Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Sorry, I encountered an error. Please try again in a moment.',
        'debug_message' => $e->getMessage() // Remove this in production
    ]);
}
?>

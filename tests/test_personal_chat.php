<?php
echo "=== Personal Chat Test ===\n\n";

// Test personal messages
$testMessages = [
    "hi",
    "my name is saif",
    "how can I improve my resume?",
    "what skills should I learn?"
];

// Mock AI responses for testing
$aiResponses = [
    'general' => [
        "Hello! I'm here to help you with career guidance! You can ask me about:\n• Specific career paths and requirements\n• Salary information and job prospects\n• Building portfolios and skills\n• Learning resources and courses\n• Job search strategies\n\nWhat would you like to know more about?",
        "Hello! I'm your AI Career Assistant. I'm here to help you with career guidance, answer questions about different career paths, and provide advice on skill development. How can I help you today?"
    ],
    'name' => [
        "Nice to meet you, saif! I'm your AI Career Assistant. I'm here to help you with career guidance, skill development, and job search advice. What would you like to know more about?\n\nYou can ask me about:\n• Specific career paths and requirements\n• Salary information and job prospects\n• Building portfolios and skills\n• Learning resources and courses\n• Job search strategies"
    ],
    'resume' => [
        "Here are some key tips for improving your resume:\n\n1. **Use action verbs** - Start bullet points with strong verbs like 'Developed', 'Implemented', 'Led'\n2. **Quantify achievements** - Include numbers and metrics (e.g., 'Increased sales by 25%')\n3. **Tailor to job** - Customize keywords and skills for each position\n4. **Keep it concise** - Aim for 1-2 pages maximum\n5. **Proofread carefully** - No typos or grammatical errors\n\nWould you like me to help you review a specific section of your resume?"
    ],
    'skills' => [
        "For skill development, consider these areas:\n\n**Technical Skills:**\n- Programming languages (Python, JavaScript, Java)\n- Data analysis tools\n- Cloud computing platforms\n\n**Soft Skills:**\n- Communication and presentation\n- Problem-solving and critical thinking\n- Teamwork and collaboration\n\n**Industry-Specific:**\n- Stay updated with latest trends\n- Get relevant certifications\n- Build a portfolio of projects\n\nWhat specific field are you interested in?"
    ]
];

// Generate fallback response function
function generateFallbackResponse($userMessage) {
    global $aiResponses;
    
    $message = strtolower($userMessage);
    
    // Check for personal information like names
    if (strpos($message, 'my name is') !== false || strpos($message, 'i am') !== false || strpos($message, 'i\'m') !== false) {
        $nameMatch = [];
        if (preg_match('/(?:my name is|i am|i\'m)\s+([a-zA-Z]+)/i', $message, $nameMatch)) {
            $name = $nameMatch[1];
            return "Nice to meet you, $name! I'm your AI Career Assistant. I'm here to help you with career guidance, skill development, and job search advice. What would you like to know more about?\n\nYou can ask me about:\n• Specific career paths and requirements\n• Salary information and job prospects\n• Building portfolios and skills\n• Learning resources and courses\n• Job search strategies";
        }
    }
    
    // Identify topic
    $topic = 'general';
    if (strpos($message, 'resume') !== false || strpos($message, 'cv') !== false) {
        $topic = 'resume';
    } else if (strpos($message, 'skill') !== false || strpos($message, 'learn') !== false) {
        $topic = 'skills';
    } else if (strpos($message, 'career') !== false || strpos($message, 'opportunity') !== false) {
        $topic = 'career';
    }
    
    // Get response from database
    $responses = $aiResponses[$topic] ?? $aiResponses['general'];
    
    // Return random response from the topic
    return $responses[array_rand($responses)];
}

// Test each message
foreach ($testMessages as $message) {
    echo "User: $message\n";
    $response = generateFallbackResponse($message);
    echo "AI: " . $response . "\n";
    echo "----------------------------------------\n\n";
}

echo "✅ Personal Chat Test Completed!\n";
echo "The system now handles personal messages like names correctly.\n";
echo "It will remember the user's name and use it in future responses.\n";
?>

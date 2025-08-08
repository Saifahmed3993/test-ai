<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Database configuration
$host = 'localhost';
$dbname = 'career_guidance';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit;
}

$email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, first_name, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Don't reveal if email exists or not for security
        echo json_encode([
            'success' => true,
            'message' => 'If an account with this email exists, a password reset link has been sent.'
        ]);
        exit;
    }

    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $tokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store reset token
    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, token, expires_at, created_at) 
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
        token = VALUES(token), 
        expires_at = VALUES(expires_at), 
        created_at = NOW()
    ");
    $stmt->execute([$user['id'], $resetToken, $tokenExpiry]);

    // Send reset email
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/career-guidance-web/reset-password.html?token=" . $resetToken;
    
    $to = $user['email'];
    $subject = "Reset your Career Guidance password";
    $message = "
    <html>
    <head>
        <title>Reset your password</title>
    </head>
    <body>
        <h2>Password Reset Request</h2>
        <p>Hi {$user['first_name']},</p>
        <p>We received a request to reset your password. Click the link below to create a new password:</p>
        <p><a href='{$resetLink}' style='background: #8B5CF6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px;'>Reset Password</a></p>
        <p>Or copy and paste this link in your browser:</p>
        <p>{$resetLink}</p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this password reset, you can safely ignore this email.</p>
        <p>Best regards,<br>The Career Guidance Team</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Career Guidance <noreply@careerguidance.com>" . "\r\n";

    // In production, use a proper email service like SendGrid or AWS SES
    // For now, we'll just log the email
    error_log("Password reset email sent to: {$email} with link: {$resetLink}");

    echo json_encode([
        'success' => true,
        'message' => 'If an account with this email exists, a password reset link has been sent.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process request. Please try again.']);
}
?>

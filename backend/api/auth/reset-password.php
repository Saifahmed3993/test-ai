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

if (!$input || !isset($input['token']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Token and password are required']);
    exit;
}

$token = $input['token'];
$password = $input['password'];

// Validate password strength
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 8 characters long']);
    exit;
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number']);
    exit;
}

try {
    // Check if token exists and is not expired
    $stmt = $pdo->prepare("
        SELECT pr.*, u.id as user_id, u.email 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or expired reset token']);
        exit;
    }

    // Hash new password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Update user password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$passwordHash, $reset['user_id']]);

    // Mark reset token as used
    $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $stmt->execute([$reset['id']]);

    // Send confirmation email
    $to = $reset['email'];
    $subject = "Your password has been reset";
    $message = "
    <html>
    <head>
        <title>Password Reset Confirmation</title>
    </head>
    <body>
        <h2>Password Reset Successful</h2>
        <p>Your password has been successfully reset.</p>
        <p>If you did not request this password reset, please contact our support team immediately.</p>
        <p>Best regards,<br>The Career Guidance Team</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Career Guidance <noreply@careerguidance.com>" . "\r\n";

    // In production, use a proper email service
    error_log("Password reset confirmation email sent to: {$to}");

    echo json_encode([
        'success' => true,
        'message' => 'Password has been reset successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to reset password']);
}
?>

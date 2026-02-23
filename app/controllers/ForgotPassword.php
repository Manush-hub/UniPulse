<?php

class ForgotPassword extends Controller {
    use Database;
    
    public function index($a = '', $b = '', $c = '') {
        // Show forgot password form
        $this->view('forgot_password');
    }
    
    public function send_reset_link() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /unipulse/public/forgotpassword');
            exit();
        }
        
        try {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->view('forgot_password', [
                    'error' => 'Please enter a valid email address'
                ]);
                return;
            }
            
            // Find user in all user tables
            $db = $this->connect();
            
            $userType = null;
            $userId = null;
            $userName = null;
            
            // Check each user table
            $tables = $this->getUserTypeMap();
            
            foreach ($tables as $type => $info) {
                $table = $info['table'];
                $nameColumn = $info['name_column'];
                $stmt = $db->prepare("SELECT id, {$nameColumn} AS name, email FROM {$table} WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    $userType = $type;
                    $userName = $user['name'];
                    break;
                }
            }
            
            // Always show success message (security practice - don't reveal if email exists)
            if (!$userType) {
                $this->view('forgot_password', [
                    'success' => 'If an account exists with this email, you will receive a password reset link shortly. Please check your email (including spam folder).'
                ]);
                return;
            }
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing unused tokens for this email
            $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ? AND used = 0");
            $stmt->execute([$email]);
            
            // Insert new reset token
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, user_type, expires_at) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $token, $userType, $expiresAt]);
            
            // Send reset email
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/unipulse/public/forgotpassword/reset?token=" . $token;
            
            $subject = "Password Reset Request - UniPulse";
            $message = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%); 
                                color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                        .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
                        .button { display: inline-block; padding: 14px 28px; background: #E87C2B; 
                                color: white; text-decoration: none; border-radius: 6px; 
                                font-weight: bold; margin: 20px 0; }
                        .footer { background: #f5f5f5; padding: 20px; text-align: center; 
                                font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
                        .warning { background: #FFF3CD; border-left: 4px solid #FFC107; 
                                 padding: 12px; margin: 15px 0; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>🔐 Password Reset Request</h1>
                        </div>
                        <div class='content'>
                            <p>Hello {$userName},</p>
                            <p>We received a request to reset your password for your UniPulse account.</p>
                            <p>Click the button below to reset your password:</p>
                            <div style='text-align: center;'>
                                <a href='{$resetLink}' class='button'>Reset Password</a>
                            </div>
                            <p>Or copy and paste this link into your browser:</p>
                            <p style='background: #f5f5f5; padding: 10px; border-radius: 4px; 
                                     word-break: break-all; font-size: 12px;'>{$resetLink}</p>
                            <div class='warning'>
                                <strong>⏰ This link will expire in 1 hour.</strong>
                            </div>
                            <p>If you didn't request this password reset, please ignore this email. 
                               Your password will remain unchanged.</p>
                        </div>
                        <div class='footer'>
                            <p><strong>UniPulse</strong> - Your University Events Hub</p>
                            <p>This is an automated email. Please do not reply to this message.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            // Send email
            $emailSent = $this->sendResetEmail($email, $userName, $subject, $message);
            
            if ($emailSent) {
                $this->view('forgot_password', [
                    'success' => 'Password reset link has been sent to your email. Please check your inbox (and spam folder). The link will expire in 1 hour.'
                ]);
            } else {
                $this->view('forgot_password', [
                    'error' => 'Failed to send reset email. Please try again later or contact support.'
                ]);
            }
            
        } catch (Exception $e) {
            $this->view('forgot_password', [
                'error' => 'An error occurred. Please try again later.'
            ]);
        }
    }
    
    public function reset($a = '', $b = '', $c = '') {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->view('reset_password', [
                'error' => 'Invalid reset link'
            ]);
            return;
        }
        
        // Handle password reset form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePasswordReset($token);
            return;
        }
        
        // Verify token
        $db = $this->connect();
        
        $stmt = $db->prepare("SELECT * FROM password_resets 
                             WHERE token = ? AND used = 0 AND expires_at > NOW()");
        $stmt->execute([$token]);
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resetRequest) {
            $this->view('reset_password', [
                'error' => 'This password reset link is invalid or has expired. Please request a new one.',
                'expired' => true
            ]);
            return;
        }
        
        // Show reset form
        $this->view('reset_password', [
            'token' => $token,
            'email' => $resetRequest['email']
        ]);
    }
    
    private function handlePasswordReset($token) {
        try {
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate passwords
            if (empty($newPassword) || empty($confirmPassword)) {
                $this->view('reset_password', [
                    'error' => 'Please fill in all fields',
                    'token' => $token
                ]);
                return;
            }
            
            if ($newPassword !== $confirmPassword) {
                $this->view('reset_password', [
                    'error' => 'Passwords do not match',
                    'token' => $token
                ]);
                return;
            }
            
            if (strlen($newPassword) < 8) {
                $this->view('reset_password', [
                    'error' => 'Password must be at least 8 characters long',
                    'token' => $token
                ]);
                return;
            }
            
            // Verify token again
            $db = $this->connect();
            
            $stmt = $db->prepare("SELECT * FROM password_resets 
                                 WHERE token = ? AND used = 0 AND expires_at > NOW()");
            $stmt->execute([$token]);
            $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resetRequest) {
                $this->view('reset_password', [
                    'error' => 'This password reset link is invalid or has expired',
                    'expired' => true
                ]);
                return;
            }
            
            // Update password in appropriate user table
            $table = $this->getTableForUserType($resetRequest['user_type']);
            if (!$table) {
                $this->view('reset_password', [
                    'error' => 'Unable to reset password for this account type. Please contact support.',
                    'token' => $token
                ]);
                return;
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("UPDATE {$table} SET password_hash = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $resetRequest['email']]);
            
            // Mark token as used
            $stmt = $db->prepare("UPDATE password_resets SET used = 1, used_at = NOW() WHERE token = ?");
            $stmt->execute([$token]);
            
            // Redirect to signin with success message
            $_SESSION['password_reset_success'] = true;
            header('Location: /unipulse/public/signin?message=password_reset_success');
            exit();
            
        } catch (Exception $e) {
            $this->view('reset_password', [
                'error' => 'An error occurred while resetting your password. Please try again.',
                'token' => $token
            ]);
        }
    }

    private function sendResetEmail($toEmail, $toName, $subject, $htmlMessage) {
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';

        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }

        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = SMTP_ENCRYPTION;
                $mail->Port = SMTP_PORT;

                $fromEmail = SMTP_FROM_EMAIL ?: SMTP_USER;
                $mail->setFrom($fromEmail, SMTP_FROM_NAME);
                $mail->addAddress($toEmail, $toName ?: $toEmail);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $htmlMessage;
                $mail->AltBody = strip_tags($htmlMessage);

                $mail->send();
                return true;
            } catch (Exception $e) {
                error_log('SMTP email error: ' . $e->getMessage());
                return false;
            }
        }

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: UniPulse <noreply@unipulse.com>\r\n";

        return mail($toEmail, $subject, $htmlMessage, $headers);
    }

    private function getUserTypeMap() {
        return [
            'admin' => ['table' => 'admins', 'name_column' => 'full_name'],
            'moderator' => ['table' => 'moderators', 'name_column' => 'full_name'],
            'public' => ['table' => 'public_users', 'name_column' => 'full_name'],
            'university' => ['table' => 'university_users', 'name_column' => 'full_name'],
            'sponsor' => ['table' => 'sponsors', 'name_column' => 'company_name'],
            'publisher' => ['table' => 'publishers', 'name_column' => 'society_name']
        ];
    }

    private function getTableForUserType($userType) {
        $map = $this->getUserTypeMap();
        return $map[$userType]['table'] ?? null;
    }
}

<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        
        // Set SMTP
        $this->mail->isSMTP();
        $this->mail->Host = 'mail.tn.gov.in';  // SMTP server
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'cams.dga';  // SMTP username
        $this->mail->Password = 'kwic>o#7Fu@g';  // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // Encryption
        $this->mail->Port = '465';  // SMTP Port
    }

    public function sendEmail($to, $subject, $body, $cc = [])
    {
        try {
            // Sender's email
            $this->mail->setFrom('cams.dga@tn.gov.in', 'CAMS');
            $this->mail->addAddress($to);  // Add recipient email

              if (!empty($cc) && is_array($cc)) {
            foreach ($cc as $ccEmail) {
                if (!empty($ccEmail)) {
                    $this->mail->addCC($ccEmail);
                }
            }
        }
            // Email content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            // Send email
            if ($this->mail->send()) {
                return 'Message has been sent';
            }
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
    
}

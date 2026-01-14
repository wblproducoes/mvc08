<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Serviço de envio de emails
 * 
 * @package App\Services
 */
class EmailService
{
    private PHPMailer $mailer;
    
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }
    
    /**
     * Configura PHPMailer
     */
    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mailer->Port = $_ENV['MAIL_PORT'];
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }
    
    /**
     * Envia email
     */
    public function send(string $to, string $subject, string $body): bool
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return false;
        }
    }
}

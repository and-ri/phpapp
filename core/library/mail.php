<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * SMTP is configured via .env (MAIL_HOST etc.); without MAIL_HOST
 * the PHP mail() transport is used.
 *
 *   $this->mail->send('user@example.com', 'Subject', '<p>HTML body</p>', [
 *       'text'        => 'Plain text alternative',
 *       'reply_to'    => 'support@example.com',
 *       'attachments' => ['/path/to/file.pdf']
 *   ]);
 */
class Mail {
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function send($to, $subject, $html, $options = array()) {
        $mailer = new PHPMailer(true);

        try {
            if ($this->env->get('MAIL_HOST')) {
                $mailer->isSMTP();
                $mailer->Host = $this->env->get('MAIL_HOST');
                $mailer->Port = (int)$this->env->get('MAIL_PORT', 587);

                if ($this->env->get('MAIL_USER')) {
                    $mailer->SMTPAuth = true;
                    $mailer->Username = $this->env->get('MAIL_USER');
                    $mailer->Password = $this->env->get('MAIL_PASS', '');
                }

                $encryption = $this->env->get('MAIL_ENCRYPTION', 'tls');

                if ($encryption == 'ssl') {
                    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($encryption == 'tls') {
                    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
            }

            $mailer->CharSet = PHPMailer::CHARSET_UTF8;

            $from = $this->env->get('MAIL_FROM', 'noreply@' . (defined('DOMAIN') && DOMAIN ? DOMAIN : 'localhost'));

            $mailer->setFrom($from, $this->env->get('MAIL_FROM_NAME', ''));

            foreach ((array)$to as $address) {
                $mailer->addAddress($address);
            }

            if (!empty($options['reply_to'])) {
                $mailer->addReplyTo($options['reply_to']);
            }

            foreach ((array)($options['attachments'] ?? array()) as $attachment) {
                $mailer->addAttachment($attachment);
            }

            $mailer->Subject = $subject;
            $mailer->isHTML(true);
            $mailer->Body = $html;
            $mailer->AltBody = !empty($options['text']) ? $options['text'] : strip_tags($html);

            $mailer->send();

            return true;
        } catch (PHPMailerException $e) {
            $this->log->error('Mail error: ' . $e->getMessage());

            return false;
        }
    }
}

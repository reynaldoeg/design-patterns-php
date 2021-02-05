<?php

namespace Styde\Strategy;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    protected $sender;
    protected $sent = [];
    protected $transport;
    protected $filename;

    protected $host;
    protected $username;
    protected $password;
    protected $port;

    public function __construct($transport = 'smtp')
    {
            $this->transport = $transport;
    }

    public function setSender($email)
    {
        $this->sender = $email;
    }
    
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }



    public function send($recipient, $subject, $body)
    {
        if ($this->transport == 'smtp') {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->Port       = $this->port;

            $mail->setFrom($this->sender);
            $mail->addAddress($recipient);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $body;

            return $mail->send();
        }

        if ($this->transport == 'array')
        {
            $this->sent[] = compact('recipient', 'subject', 'body');
            return true;
        }

        if ($this->transport == 'file') {
            $data = [
                'New Email',
                "Recipient: {$recipient}",
                "Subject: {$subject}",
                "Body: {$body}",
            ];

            file_put_contents($this->filename, implode("\n", $data), FILE_APPEND);
        }

    }

    public function sent()
    {
        return $this->sent;
    }
}

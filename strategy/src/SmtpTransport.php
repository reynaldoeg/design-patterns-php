<?php


namespace Styde\Strategy;


use PHPMailer\PHPMailer\PHPMailer;

class SmtpTransport extends Transport
{
    protected $host;
    protected $username;
    protected $password;
    protected $port;

    /**
     * SmtpTransport constructor.
     * @param $host
     * @param $username
     * @param $password
     * @param $port
     */
    public function __construct($host, $username, $password, $port)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }


    public function send($recipient, $subject, $body, $sender)
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $this->host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->username;
        $mail->Password   = $this->password;
        $mail->Port       = $this->port;

        $mail->setFrom($sender);
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        return $mail->send();
    }
}

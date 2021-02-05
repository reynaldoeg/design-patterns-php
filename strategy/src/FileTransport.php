<?php


namespace Styde\Strategy;


class FileTransport extends Transport
{
    protected $filename;

    /**
     * FileTransport constructor.
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function send($recipient, $subject, $body, $sender)
    {
        $data = [
            'New Email',
            "Recipient: {$recipient}",
            "Subject: {$subject}",
            "Body: {$body}",
        ];

        return file_put_contents($this->filename, implode("\n", $data), FILE_APPEND);
    }
}

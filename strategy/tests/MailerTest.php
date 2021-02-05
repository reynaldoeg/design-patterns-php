<?php


namespace Styde\Strategy\Tests;

use Styde\Strategy\Mailer;
use Styde\Strategy\FileTransport;
use Styde\Strategy\SmtpTransport;
use Styde\Strategy\ArrayTransport;
use StephaneCoinon\Mailtrap\Inbox;
use StephaneCoinon\Mailtrap\Client;
use StephaneCoinon\Mailtrap\Model;
use Styde\Strategy\Transport;

/**
 * Class MailerTest
 * @package Styde\Strategy\Tests
 * @covers Mailer
 */
class MailerTest extends TestCase
{
    //* @test */
    function test_it_stores_the_sent_email_in_an_array()
    {
        $mailer = new Mailer($transport = new ArrayTransport());
        $mailer->setSender('reynaldoegesparza@gmail.com');

        $mailer->send(
            'reynaldoeg_esparza@hotmail.com',
            'An example message',
            'The content of the message'
        );

        $sent = $transport->sent();

        $this->assertCount(1, $sent);
        $this->assertSame('reynaldoeg_esparza@hotmail.com', $sent[0]['recipient']);
        $this->assertSame('An example message', $sent[0]['subject']);
        $this->assertSame('The content of the message', $sent[0]['body']);
    }

    //* @test */
    function test_it_stores_the_sent_email_in_a_log_file()
    {
        $filename = __DIR__.'/../storage/test.txt';
        @unlink($filename);


        $mailer = new Mailer(new FileTransport($filename));
        $mailer->setSender('reynaldoegesparza@gmail.com');

        $mailer->send(
            'reynaldoeg_esparza@hotmail.com',
            'An example message',
            'The content of the message'
        );

        $content = file_get_contents($filename);

        $this->assertStringContainsString('Recipient: reynaldoeg_esparza@hotmail.com', $content);
        $this->assertStringContainsString('Subject: An example message', $content);
        $this->assertStringContainsString('Body: The content of the message', $content);
    }

    //* @test */
    function test_it_sends_emails_using_smtp()
    {
        // - Given / Setup / Arrange

        // Instantiate Mailtrap API client
        $client = new Client('4be0f3bbd0fe1bf4a90043e9472dad17');

        // Boot API models
        Model::boot($client);

        // Fetch an inbox by its id
        $inbox = Inbox::find(1211824);
        $inbox->empty();

        // - When / Act

        $mailer = new Mailer(new SmtpTransport('smtp.mailtrap.io', 'c4fe0d17fb3ca7', 'a6e7d16cb434ab', '25'));
        $mailer->setSender('reynaldoegesparza@gmail.com');

        $sent = $mailer->send(
            'reynaldoeg_esparza@hotmail.com',
            'An example message',
            'The content of the message'
        );

        // - Then / Assert
        $this->assertTrue($sent);

        // Get the last (newest) message in an inbox
        $newestMessage = $inbox->lastMessage();

        $this->assertNotNull($newestMessage);
        $this->assertSame(['reynaldoeg_esparza@hotmail.com'], $newestMessage->recipientEmails());
        $this->assertSame('An example message', $newestMessage->subject());
        $this->assertSame('The content of the message', trim($newestMessage->textBody()));
    }
}

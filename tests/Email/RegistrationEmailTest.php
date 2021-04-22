<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/path_here/RegistrationEmailTest.php

namespace tests\Email;

use App\Services\EmailerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class RegistrationEmailTest extends TestCase
{

    public function testSimpleMessage()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $mailer = new EmailerService($symfonyMailer);
        $mailParams = [
            'template' => 'Email/non_user_forgotten_password.html.twig',
            'context' => ['supportEmail' => 'admin@bogus.info'],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

}

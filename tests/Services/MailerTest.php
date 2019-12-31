<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Services/MailerTest.php

namespace App\Tests\Services;

use App\Entity\Nonprofit;
use App\Entity\Staff;
use App\Services\EmailerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailerTest extends TestCase
{

    public function testFogottenPasswordNotAUserEmail()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $mailer = new EmailerService($symfonyMailer, 'admin@bogus.info', 'admin@bogus.info');
        $mailParams = [
            'view' => 'Email/non_user_forgotten_password.html.twig',
            'context' => ['supportEmail' => 'admin@bogus.info'],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

    public function testFogottenPasswordUserEmail()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $mailer = new EmailerService($symfonyMailer, 'admin@bogus.info', 'admin@bogus.info');
        $mailParams = [
            'view' => 'Email/forgotten.html.twig',
            'context' => [
                'fname' => 'Foist',
                'token' => 'token',
                'expiresAt' => new \DateTime(),
            ],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

    public function testNonprofitRegistrationEmail()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $npo = new Nonprofit();
        $npo->setOrgname('UrNameHere');
        $npo->setEin('999999999');
        $mailer = new EmailerService($symfonyMailer, 'admin@bogus.info', 'admin@bogus.info');
        $mailParams = [
            'view' => 'Email/staff_confirmation.html.twig',
            'context' => [
                'fname' => 'Foist',
                'token' => 'token',
                'expiresAt' => new \DateTime(),
                'orgname' => $npo->getOrgname(),
            ],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

    public function testVolunteerRegistrationEmail()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $mailer = new EmailerService($symfonyMailer, 'admin@bogus.info', 'admin@bogus.info');
        $mailParams = [
            'view' => 'Email/volunteer_confirmation.html.twig',
            'context' => [
                'fname' => 'Foist',
                'token' => 'token',
                'expiresAt' => new \DateTime(),
            ],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

    public function testNewNonprofitActivationEmail()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $npo = new Nonprofit();
        $npo->setOrgname('UrNameHere');
        $staff = new Staff();
        $staff->setFname('Benny');
        $npo->setStaff($staff);
        $mailer = new EmailerService($symfonyMailer, 'admin@bogus.info', 'admin@bogus.info');
        $mailParams = [
            'view' => 'Email/new_nonprofit_notice.html.twig',
            'context' => ['npo' => $npo, 'staff' => $npo->getStaff(),],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

    public function testActivationEmail()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
                ->method('send');

        $npo = new Nonprofit();
        $npo->setOrgname('UrNameHere');
        $staff = new Staff();
        $staff->setFname('Benny');
        $npo->setStaff($staff);
        $mailer = new EmailerService($symfonyMailer, 'admin@bogus.info', 'admin@bogus.info');
        $mailParams = [
            'view' => 'Email/nonprofit_activated.html.twig',
            'context' => ['npo' => $npo, 'staff' => $npo->getStaff(),],
            'recipient' => 'bborko@bogus.info',
            'subject' => 'Test message',
        ];
        $email = $mailer->appMailer($mailParams);

        $this->assertSame('Test message', $email->getSubject());
    }

}

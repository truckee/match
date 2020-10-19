<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/RegistrationTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Registration
 */
class RegistrationTest extends WebTestCase
{
    public function setup(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testEmptyOrNonexistentToken()
    {
        $this->client->request('GET', '/register/confirm');

        $this->assertStringContainsString('Registration status cannot be determined', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/register/confirm/blahblah');

        $this->assertStringContainsString('Invalid registration data', $this->client->getResponse()->getContent());
    }

    public function testExpiredToken()
    {
        $this->client->request('GET', '/register/confirm/fedcba');

        $this->assertStringContainsString('Please register again', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Become a volunteer', $this->client->getResponse()->getContent());
    }

    public function testNotYetConfirmedAndConfirmation()
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'random@bogus.info',
            'password' => '123Abc',
        ]);

        $this->assertStringContainsString('Account has not been confirmed', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/register/confirm/abcdef');

        $this->assertStringContainsString('Account is confirmed', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Sign in', $this->client->getResponse()->getContent());

        $this->client->submitForm('Sign in', [
            'email' => 'random@bogus.info',
            'password' => '123Abc',
        ]);

        $this->assertStringContainsString('Volunteer Connections for Western Nevada', $this->client->getResponse()->getContent());
    }

    public function testResetPasswordNoToken()
    {
        $this->client->request('GET', '/register/reset');

        $this->assertStringContainsString('Registration status cannot be determined', $this->client->getResponse()->getContent());
    }

    public function testResetPasswordWrongToken()
    {
        $this->client->request('GET', '/register/reset/wassup');

        $this->assertStringContainsString('Invalid registration data', $this->client->getResponse()->getContent());
    }

    public function testResetPasswordExpiredToken()
    {
        $this->client->request('GET', '/register/reset/fedcba');

        $this->assertStringContainsString('Password link has expired', $this->client->getResponse()->getContent());
    }

    public function testResetPasswordValidTokenAndLogIn()
    {
        $this->client->request('GET', '/register/reset/ghijkl');

        $this->assertStringContainsString('Set new password', $this->client->getResponse()->getContent());

        $this->client->submitForm('Save', [
            'new_password[plainPassword][first]' => 'Abc123',
            'new_password[plainPassword][second]' => 'Abc123',
        ]);

        $this->assertStringContainsString('Your password has been updated', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'pseudo@bogus.info',
            'password' => 'Abc123',
        ]);
        
        $this->assertStringContainsString('Volunteer Connections for Western Nevada', $this->client->getResponse()->getContent());
    }
    
    public function testFogottenPasswordNotAUser()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/register/forgot');
        $this->client->submitForm('Submit', [
            'user_email[email]' => 'swimming@pool.com',
        ]);
        
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        
        $this->assertStringContainsString('email is not recognized', $message->getBody());
    }
    
    public function testFogottenPasswordUser()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/register/forgot');
        $this->client->submitForm('Submit', [
            'user_email[email]' => 'random@bogus.info',
        ]);
        
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        
        $this->assertStringContainsString('to change your password', $message->getBody());
    }
    
    public function testNewNonprofitActivationEmail()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/register/confirm/tuvxyz');
        
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        
        $this->assertStringContainsString('new nonprofit has submitted registration', $message->getBody());
    }
}

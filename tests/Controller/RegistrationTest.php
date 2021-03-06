<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/RegistrationTest.php

namespace Tests\Controller;

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
        $this->client->request('GET', '/');
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

        $this->assertStringContainsString('Registration has expired', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Volunteer Connections for Western Nevada', $this->client->getResponse()->getContent());
    }

    public function testNotYetConfirmedAndConfirmation()
    {
        $this->client->clickLink('Log in');
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

        $this->client->clickLink('Log in');
        $this->client->submitForm('Sign in', [
            'email' => 'pseudo@bogus.info',
            'password' => 'Abc123',
        ]);

        $this->assertStringContainsString('Volunteer Connections for Western Nevada', $this->client->getResponse()->getContent());
    }

    public function testFogottenPasswordNotAUser()
    {
        $this->client->clickLink('Log in');
        $this->client->followRedirects(false);
        $this->client->clickLink('Forgot password?');
        $this->client->submitForm('Submit', [
            'user_email[email]' => 'swimming@pool.com',
        ]);

        $this->assertResponseRedirects();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);

        $this->assertStringContainsString('email is not recognized', $email->getHtmlBody());
    }

    public function testFogottenPasswordUser()
    {
        $this->client->clickLink('Log in');
        $this->client->followRedirects(false);
        $this->client->clickLink('Forgot password?');
        $this->client->submitForm('Submit', [
            'user_email[email]' => 'random@bogus.info',
        ]);

        $this->assertResponseRedirects();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($email, 'Subject', 'Volunteer Connections forgotten password');

        $this->assertStringContainsString('to change your password', $email->getHtmlBody());
    }

    public function testNewNonprofitActivationEmail()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/register/confirm/tuvxyz');

        $this->assertResponseRedirects();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);

        $this->assertStringContainsString('new nonprofit has submitted registration', $email->getHtmlBody());
    }

//    public function testNonUserRegistration()
//    {
//        $this->client->request('GET', '/register');
//
//        $this->assertStringContainsString('Registration is not available', $this->client->getResponse()->getContent());
//
//        $this->client->request('GET', '/register/person');
//
//        $this->assertStringContainsString('Registration is not available', $this->client->getResponse()->getContent());
//
//        $this->client->request('GET', '/register/person/admin');
//
//        $this->assertStringContainsString('Registration is not available', $this->client->getResponse()->getContent());
//
//        $this->client->request('GET', '/register/person/staff');
//
//        $this->assertStringContainsString('Registration is not available', $this->client->getResponse()->getContent());
//    }
}

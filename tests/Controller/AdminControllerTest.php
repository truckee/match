<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/AdminControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Admin
 */
class AdminControllerTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'admin@bogus.info',
            'password' => '123Abc',
        ]);
        $this->crawler = $this->client->request('GET', '/admin');
    }

    public function testDashboard()
    {
        $this->client->request('GET', '/admin/dashboard');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Admin dashboard', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/admin');
        $this->assertStringContainsString('Nonprofit', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Activate', $this->client->getResponse()->getContent());
    }

    public function testActivateNonprofit()
    {
        $crawler = $this->client->request('GET', '/admin');
        $this->client->clickLink('Activate');

        $this->assertStringContainsString('Nonprofit activated!', $this->client->getResponse()->getContent());
    }

    public function testDeactivateNonprofit()
    {
        $btn = $this->crawler->filter(".btn:contains('Deactivate')")->link();
        $this->client->click($btn);

        $this->assertStringContainsString('Nonprofit deactivated', $this->client->getResponse()->getContent());
    }

    public function testActivationEmail()
    {
        $this->client->followRedirects(false);
        $this->client->clickLink('Activate');
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertStringContainsString('You will now be able post opportunities', $message->getBody());
    }

    public function testLockAndUnlockUser()
    {
        $this->client->clickLink('Volunteer');

        $this->assertStringContainsString('Locked', $this->client->getResponse()->getContent());

        $this->client->clickLink('Lock');

        $this->assertStringContainsString('is now locked', $this->client->getResponse()->getContent());

        $this->client->clickLink('Unlock');

        $this->assertStringContainsString('is now unlocked', $this->client->getResponse()->getContent());
    }

    public function testStaffReplacementEmailSent()
    {
        $this->client->clickLink('Staff');

//        $this->assertStringContainsString('Locking staff deactivates nonprofit', $this->client->getResponse()->getContent());

        $this->client->clickLink('Replace');

        $this->assertStringContainsString('Replacement for', $this->client->getResponse()->getContent());
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'ugar@bogus.info'
        ]);

        $this->assertStringContainsString('Replacement email sent', $this->client->getResponse()->getContent());
    }

    public function testStaffReplacementEmailToken()
    {
        $this->client->clickLink('Staff');
        $this->client->followRedirects(false);
        $this->client->clickLink('Replace');
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'ugar@bogus.info'
        ]);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $body = $message->getBody();
        $pos = strpos($body, '">link') - 32;
        $token = substr($body, $pos, 32);

        $this->client->request('GET', '/logout');

        $this->client->followRedirects(true);
        $this->client->request('GET', '/register/reset/' . $token);
        $this->client->submitForm('Save', [
            'new_password[plainPassword][first]' => 'Abc123',
            'new_password[plainPassword][second]' => 'Abc123',
        ]);

        $this->assertStringContainsString('You are now the registered representative', $this->client->getResponse()->getContent());
    }

    public function testInviteExistingEmail()
    {
        $this->client->request('GET', '/');
        $this->client->clickLink('Invite new admin');
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'staff@bogus.info'
        ]);

        $this->assertStringContainsString('Email already registered', $this->client->getResponse()->getContent());
    }

    public function testInviteNewAdminEmail()
    {
        $this->client->clickLink('Home');
        $this->client->clickLink('Invite new admin');
        $this->client->followRedirects(false);
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'startrek@bogus.info'
        ]);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertStringContainsString('You are invited to be an admin user', $message->getBody());

        $this->client->followRedirects(true);
        $body = $message->getBody();
        $pos = strpos($body, '">link') - 32;
        $token = substr($body, $pos, 32);
        $this->client->request('GET', '/register/confirm/' . $token);

        $this->assertStringContainsString('thank you for accepting. You may now log i', $this->client->getResponse()->getContent());
    }

    public function testNonAdminValidToken()
    {
        $this->client->request('GET', '/register/confirm/invalidToken');

        $this->assertStringContainsString('Invalid registration data', $this->client->getResponse()->getContent());
    }

    public function testExpiredToken()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/register/confirm/whoami');
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertStringContainsString('has just failed due to it having expired', $message->getBody());
    }

    public function testRegisterNewAdmin() //mynameis
    {
        $this->client->request('GET', '/register/confirm/mynameis');
        $this->client->submitForm('Save', [
            'new_password[plainPassword][first]' => 'Abc123',
            'new_password[plainPassword][second]' => 'Abc123',
        ]);

        $this->assertStringContainsString('thank you for accepting', $this->client->getResponse()->getContent());
    }

    public function testAdminScreen()
    {
        $this->client->request('GET', '/admin');
        $this->client->clickLink('Admin');

        $this->assertStringContainsString('ROLE_SUPER_ADMIN', $this->client->getResponse()->getContent());
    }

    public function testEnableDisableFails()
    {
        $this->client->request('GET', '/admin');
        $crawler = $this->client->clickLink('Admin');
        $link = $crawler->filter('#Adminenabled1')->link();
        $this->client->click($link);

        $this->assertStringContainsString('Benny Borko cannot be disabled', $this->client->getResponse()->getContent());
    }

    public function testEnableDisableSucceeds()
    {
        $this->client->request('GET', '/admin');
        $crawler = $this->client->clickLink('Admin');
        $link = $crawler->filter('#Adminenabled2')->link();
        $str1 = $crawler->filter('#Adminenabled2 > input[type=checkbox]')->attr('checked');

        $this->assertStringContainsString('checked', $str1);

        $this->client->click($link);
        $this->client->request('GET', '/admin');
        $str2 = $crawler->filter('#Adminenabled3 > input[type=checkbox]')->attr('checked');

        $this->assertNull($str2);
    }

    public function testFocusAdd()
    {
        $this->client->request('GET', '/admin');
        $this->client->clickLink('Focus');
        $crawler = $this->client->clickLink('Add Focus');
        $form = $crawler->selectButton('Create')->eq(1)->form();
        $form['Focus[focus]'] = 'Another focus';
        $form['Focus[enabled]']->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('<strong>4</strong>', $this->client->getResponse()->getContent());
    }

    public function testSkillAdd()
    {
        $this->client->request('GET', '/admin');
        $this->client->clickLink('Skill');
        $crawler = $this->client->clickLink('Add Skill');
        $form = $crawler->selectButton('Create')->eq(1)->form();
        $form['Skill[skill]'] = 'Another skill';
        $form['Skill[enabled]']->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('<strong>4</strong> results', $this->client->getResponse()->getContent());
    }

}

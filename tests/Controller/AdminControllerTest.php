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
        
        $this->assertStringContainsString('Locked?', $this->client->getResponse()->getContent());

        $this->client->clickLink('Lock');
        
        $this->assertStringContainsString('is now locked', $this->client->getResponse()->getContent());

        $this->client->clickLink('Unlock');
        
        $this->assertStringContainsString('is now unlocked', $this->client->getResponse()->getContent());
    }
    
    public function testReplaceStaff()
    {
        $this->client->clickLink('Staff');
        
        $this->assertStringContainsString('Locking staff deactivates nonprofit', $this->client->getResponse()->getContent());

        $this->client->clickLink('Replace');

        $this->assertStringContainsString('Replacement for', $this->client->getResponse()->getContent());
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'ugar@bogus.info'
        ]);
        
        $this->assertStringContainsString('Replacement email sent', $this->client->getResponse()->getContent());
    }
    
    public function testInviteExistingEmail()
    {
        $this->client->request('GET', '/admin/invite');
        $this->client->followRedirects(false);
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'staff@bogus.info'
        ]);
        
        $this->assertStringContainsString('Email already registered', $this->client->getResponse()->getContent());
    }
    
    public function testInviteNewAdminEmail()
    {
        $this->client->request('GET', '/admin/invite');
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
    }
    
    public function testNonAdminValidToken()
    {
        $this->client->request('GET', '/register/invite/abcdef');
        
        $this->assertStringContainsString('Invalid registration data', $this->client->getResponse()->getContent());
    }
    
    public function testExpiredToken()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/register/invite/whoami');
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        
        $this->assertStringContainsString('has just failed due to it having expired', $message->getBody());        
    }
    
    public function testRegisterNewAdmin() //mynameis
    {
        $this->client->request('GET', '/register/invite/mynameis');
            $this->client->submitForm('Save', [
            'new_password[plainPassword][first]' => 'Abc123',
            'new_password[plainPassword][second]' => 'Abc123',
        ]);
    
        $this->assertStringContainsString('Your admin account is created', $this->client->getResponse()->getContent());
    }
    
    public function testAdminScreen()
    {
        $this->client->request('GET', '/admin');
        $this->client->clickLink('Admin');
    
        $this->assertStringContainsString('Enable/Disable', $this->client->getResponse()->getContent());
    }
    
    public function testEnableDisableFails()
    {
        $this->client->request('GET', '/admin');
        $crawler = $this->client->clickLink('Admin');
        $link = $crawler->filter('.action-admin_enabler')->eq(0)->link();
        $this->client->click($link);
        
        $this->assertStringContainsString('Benny Borko cannot be disabled', $this->client->getResponse()->getContent());
    }
    
    public function testEnableDisableSucceeds()
    {
        $this->client->request('GET', '/admin');
        $crawler = $this->client->clickLink('Admin');
        $link = $crawler->filter('.action-admin_enabler')->eq(1)->link();
        $this->client->click($link);
        $this->client->request('GET', '/admin');
        $crawler2 = $this->client->clickLink('Admin');
        $badge = $crawler2->filter('*[data-id="2"]');
        
        $this->assertStringContainsString('No', $badge->text());
    }
    
    public function testFocusAdd()
    {
        $this->client->request('GET', '/admin');
        $this->client->clickLink('Focus');
        $crawler = $this->client->clickLink('Add Focus');
        $form = $crawler->selectButton('Save changes')->form();
        $form['focus[focus]'] = 'Another focus';
        $form['focus[enabled]']->tick();
        $this->client->submit($form);
        file_put_contents('g:\\documents\\response.html', $this->client->getResponse()->getContent());
        
        $this->assertStringContainsString('<strong>4</strong> results', $this->client->getResponse()->getContent());
    }
    
    public function testSkillAdd()
    {
        $this->client->request('GET', '/admin');
        $this->client->clickLink('Skill');
        $crawler = $this->client->clickLink('Add Skill');
        $form = $crawler->selectButton('Save changes')->form();
        $form['skill[skill]'] = 'Another skill';
        $form['skill[enabled]']->tick();
        $this->client->submit($form);
        file_put_contents('g:\\documents\\response.html', $this->client->getResponse()->getContent());
        
        $this->assertStringContainsString('<strong>4</strong> results', $this->client->getResponse()->getContent());
    }
 }

<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/AdminControllerTest.php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function setup(): void
    {
        $this->fixtures = $this->loadFixtures([
                    'App\DataFixtures\Test\OptionsFixture',
                    'App\DataFixtures\Test\NonprofitFixture',
                    'App\DataFixtures\Test\UserFixture',
                ])
                ->getReferenceRepository();
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'admin@bogus.info',
            'password' => '123Abc',
        ]);
    }
    
    public function testLogin()
    {
        $this->client->request('GET', '/admin/dashboard');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testActivateNonprofit()
    {
        $id = $this->fixtures->getReference('marmot')->getId();
        
        $this->client->request('GET', '/admin/status/3456789');
        
        $this->assertStringContainsString('Nonprofit not found', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/admin/status/' . $id);
        
        $this->assertStringContainsString('Nonprofit activated!', $this->client->getResponse()->getContent());
    }
    
    public function testActivation()
    {
        $id = $this->fixtures->getReference('marmot')->getId();
        $this->client->request('GET', '/admin/status/' . $id);
        
        $this->assertStringContainsString('Nonprofit activated!', $this->client->getResponse()->getContent());        
    }
    
    public function testDeactivateNonprofit()
    {
        $id = $this->fixtures->getReference('marmot')->getId();
        $this->client->request('GET', '/admin/status/' . $id);
        $this->client->request('GET', '/admin/status/' . $id);
        
        $this->assertStringContainsString('Nonprofit deactivated', $this->client->getResponse()->getContent()); 
        
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email'=>'unknown@bogus.info',
            'password'=>'123Abc',
        ]);
        
        $this->assertStringContainsString('Account is locked', $this->client->getResponse()->getContent()); 
    }
    
    public function testEasyAdminPage()
    {
        $this->client->request('GET', '/admin');
        
        $this->assertStringContainsString('Benny Borko', $this->client->getResponse()->getContent());
    }
    
    public function testLockUser()
    {
        $id = $this->fixtures->getReference('volunteer')->getId();
        $this->client->request('GET', '/admin/lock/' . $id);
        
        $this->assertStringContainsString('is now locked', $this->client->getResponse()->getContent());
        
        $this->client->request('GET', '/admin/lock/' . $id);
        
        $this->assertStringContainsString('is now unlocked', $this->client->getResponse()->getContent());
    }
    
    public function testReplaceStaff()
    {
        $id = $this->fixtures->getReference('staff')->getId();
        $this->client->request('GET', '/admin/replaceStaff/' . $id);
        $this->client->submitForm('Save', [
            'user[fname]' => 'Useless',
            'user[sname]' => 'Garbage',
            'user[email]' => 'ugar@bogus.info'
        ]);
        
        $this->assertStringContainsString('Replacement email sent', $this->client->getResponse()->getContent());
    }
    
// this test creates spooled messages regardless of the disable_delivery setting
// use it sparingly    
//    public function testSpoolEmail()
//    {
//        $this->client->followRedirects(false);
//        $this->client->request('GET', '/admin/spool');
//        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
//
//        $this->assertSame(1, $mailCollector->getMessageCount());      
//    }
}

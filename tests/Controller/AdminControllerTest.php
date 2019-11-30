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
        $this->client->request('GET', '/admin');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testActivateNonprofit()
    {
        $id = $this->fixtures->getReference('marmot')->getId();
        
        $this->client->request('GET', '/admin/activate/3456789');
        
        $this->assertStringContainsString('Nonprofit not found', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/admin/activate/' . $id);
        
        $this->assertStringContainsString('Nonprofit activated!', $this->client->getResponse()->getContent());
    }
    
    public function testActivationEmail()
    {
        $id = $this->fixtures->getReference('marmot')->getId();
        $this->client->followRedirects(false);
        $this->client->request('GET', '/admin/activate/' . $id);
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        
        $this->assertStringContainsString('You will now be able post opportunites', $message->getBody());        
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

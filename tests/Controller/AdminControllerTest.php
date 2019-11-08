<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/AdminController.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function setup(): void
    {
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
        
        $this->assertStringContainsString('Admin functions will appear here', $this->client->getResponse()->getContent());
    }
    
    public function testActivateNonprofit()
    {
        
        $this->client->request('GET', '/admin/activate/3456789');
        
        $this->assertStringContainsString('Nonprofit not found', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/admin/activate/123456789');
        
        $this->assertStringContainsString('It worked?!', $this->client->getResponse()->getContent());
        
        
    }
    
    public function testActivationEmail()
    {
        $this->client->followRedirects(false);
        $this->client->request('GET', '/admin/activate/123456789');
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

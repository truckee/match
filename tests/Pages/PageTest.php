<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/tests/Pages/PageTest.php

namespace App\Tests\Pages;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */
class PageTest extends WebTestCase
{
    public function setup() {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }
    public function testHomePage() {
        
        $this->client->request('GET', '/');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Volunteer Connections', $this->client->getResponse()->getContent());
    }
    
    public function testVolunteerPage() {
        $this->client->request('GET', '/volunteer');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Search for an opportunity', $this->client->getResponse()->getContent());
        
        $crawler = $this->client->clickLink('Become a volunteer');
        $this->assertContains('First name', $this->client->getResponse()->getContent());
    }
    
    public function testNonprofitPage() {
        $this->client->request('GET', '/nonprofit');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Nonprofit organizations new to this site', $this->client->getResponse()->getContent());
        
        $crawler = $this->client->clickLink('Register a nonprofit');
        $this->assertContains('First name', $this->client->getResponse()->getContent());
    }
}

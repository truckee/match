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
    public function setup() : void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }
    
    public function testHomePage()
    {
        $this->client->request('GET', '/');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Volunteer Connections', $this->client->getResponse()->getContent());
    }
    
    public function testVolunteerPage()
    {
        $this->client->request('GET', '/volunteer');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Search for an opportunity', $this->client->getResponse()->getContent());
        
        $crawler = $this->client->clickLink('Become a volunteer');
        $this->assertStringContainsString('First name', $this->client->getResponse()->getContent());
    }
    
    public function testNonprofitPage()
    {
        $this->client->request('GET', '/nonprofit');
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Nonprofit organizations new to this site', $this->client->getResponse()->getContent());
        
        $crawler = $this->client->clickLink('Register a nonprofit');
        $this->assertStringContainsString('First name', $this->client->getResponse()->getContent());
    }
}

<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/NonprofitController.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */
class NonprofitControllerTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testView()
    {
        $this->client->request('GET', '/opportunity/search');
        $this->client->submitForm('submit');
        
        $this->assertStringContainsString('Turkey Fund', $this->client->getResponse()->getContent());
        
        $this->client->clickLink('Turkey Fund');
        
        $this->assertStringContainsString('Address', $this->client->getResponse()->getContent());
    }
    
    public function testNonprofitNotFound()
    {
        $this->client->request('GET', '/nonprofit/view/6');
        
        $this->assertStringContainsString('Nonprofit not found', $this->client->getResponse()->getContent());        
    }

}

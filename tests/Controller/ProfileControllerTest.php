<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/ProfileControllerTest.php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function setup(): void
    {
//        $this->loadFixtures();
        $this->client = static::createClient();
        $this->client->followRedirects();
    }
    
    public function testVolunteerProfile()
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'pseudo@bogus.info',
            'password' => '123Abc',
        ]);
        $this->client->request('GET', '/profile');
        
        $this->assertStringContainsString('Very Bogus profile', $this->client->getResponse()->getContent());
        
    }
    
    public function testNonprofitProfile()
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'staff@bogus.info',
            'password' => '123Abc',
        ]);
        $this->client->request('GET', '/profile');
        
        $this->assertStringContainsString('Turkey Fund profile', $this->client->getResponse()->getContent());
        
//        $this->client->submitForm()
    }
    
}

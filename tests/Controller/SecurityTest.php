<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/SecurityTest.php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 */
class SecurityTest extends WebTestCase
{
    use FixturesTrait;

    public function setup() : void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }
    
    public function testLogin()
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'admin@bogus.info',
            'password' => '123Abc',
        ]);
        
        $this->assertStringContainsString('Admin dashboard', $this->client->getResponse()->getContent());
    }
}

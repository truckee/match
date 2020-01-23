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
        $this->fixtures = $this->loadFixtures([
                    'App\DataFixtures\Test\OptionsFixture',
                    'App\DataFixtures\Test\NonprofitFixture',
                    'App\DataFixtures\Test\UserFixture',
                ]);
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testVolunteerProfile()
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'volunteer@bogus.info',
            'password' => '123Abc',
        ]);
        $this->client->request('GET', '/profile/person');

        $this->assertStringContainsString('Exceptionally Bogus profile', $this->client->getResponse()->getContent());
file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
        $this->client->submitForm('Save', [
            'user[fname]' => 'Unchained',
        ]);

        $this->assertStringContainsString('Profile updated', $this->client->getResponse()->getContent());
    }

    public function testNonprofitProfile()
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'staff@bogus.info',
            'password' => '123Abc',
        ]);
        $this->client->request('GET', '/profile/nonprofit');

        $this->assertStringContainsString('Turkey Fund profile', $this->client->getResponse()->getContent());

        $this->client->submitForm('Save');

        $this->assertStringContainsString('Profile updated', $this->client->getResponse()->getContent());
    }

}

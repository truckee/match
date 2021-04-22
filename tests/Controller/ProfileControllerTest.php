<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/ProfileControllerTest.php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Profile
 */
class ProfileControllerTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
        $this->client->request('GET', '/');
    }

    public function testVolunteerProfile()
    {
        $this->client->clickLink('Log in');
        $this->client->submitForm('Sign in', [
            'email' => 'volunteer@bogus.info',
            'password' => '123Abc',
        ]);
        $this->client->request('GET', '/profile/person');

        $this->assertStringContainsString('Exceptionally Bogus profile', $this->client->getResponse()->getContent());

        $this->client->submitForm('Save', [
            'user[fname]' => 'Unchained',
        ]);

        $this->assertStringContainsString('Profile updated', $this->client->getResponse()->getContent());
    }

    public function testNonprofitProfile()
    {
        $this->client->clickLink('Log in');
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

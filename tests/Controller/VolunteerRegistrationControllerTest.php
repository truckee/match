<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/VolunteerRegistrationControllerTest.php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VolunteerRegistrationControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function setup(): void
    {
//        $this->loadFixtures();
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testVolunteerPageValidation()
    {
        $this->client->request('GET', '/register/volunteer');

        $this->client->submitForm('Save');
        $this->assertStringContainsString('First name is required', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Last name is required', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Email is required', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Password may not empty', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('At least one focus', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('At least one skill', $this->client->getResponse()->getContent());
    }

    public function testVolunteerRegistration()
    {
        $content = $this->volunteerRegistration();

        $this->assertStringContainsString('registration confirmation has been sent', $content);
    }

    public function testDuplicateVolunteerRegistration()
    {
        $content = $this->volunteerRegistration();

        $this->assertStringContainsString('registration confirmation has been sent', $content);

        $dupe = $this->volunteerRegistration();

        $this->assertStringContainsString('Email already registered', $dupe);
    }

    // Note: this test uses text from templates/Email/volunteer_confirmation.html.twig
    public function testVolunteerRegistrationEmail()
    {
        $this->client->followRedirects(false);
        $crawler = $this->client->request('GET', '/register/volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['new_user[fname]'] = 'Benny';
        $form['new_user[sname]'] = 'Borko';
        $form['new_user[email]'] = 'bborko@bogus.info';
        $form['new_user[plainPassword][first]'] = '123Abc';
        $form['new_user[plainPassword][second]'] = '123Abc';
        $form['new_user[focuses]'][0]->tick();
        $form['new_user[skills]'][0]->tick();
        $this->client->submit($form);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertStringContainsString('you will begin to receive emails', $message->getBody());
    }

    private function volunteerRegistration()
    {
        $crawler = $this->client->request('GET', '/register/volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['new_user[fname]'] = 'Benny';
        $form['new_user[sname]'] = 'Borko';
        $form['new_user[email]'] = 'bborko@bogus.info';
        $form['new_user[plainPassword][first]'] = '123Abc';
        $form['new_user[plainPassword][second]'] = '123Abc';
        $form['new_user[focuses]'][0]->tick();
        $form['new_user[skills]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }

}

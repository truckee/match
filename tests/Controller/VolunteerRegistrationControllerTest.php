<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/VolunteerRegistrationControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Volunteer
 */
class VolunteerRegistrationControllerTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->client->request('GET', '/');
    }

    public function testVolunteerPageValidation()
    {
        $this->client->clickLink('Volunteer');
        $this->client->ClickLink('Become a volunteer');
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
        $this->client->clickLink('Volunteer');
        $crawler = $this->client->ClickLink('Become a volunteer');
        $this->client->followRedirects(false);
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['user[fname]'] = 'Benny';
        $form['user[sname]'] = 'Borko';
        $form['user[email]'] = 'bborko@bogus.info';
        $form['user[plainPassword][first]'] = '123Abc';
        $form['user[plainPassword][second]'] = '123Abc';
        $form['user[focuses]'][0]->tick();
        $form['user[skills]'][0]->tick();
        $this->client->submit($form);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertStringContainsString('you will begin to receive emails', $message->getBody());
    }

    private function volunteerRegistration()
    {
        $this->client->clickLink('Volunteer');
        $crawler = $this->client->ClickLink('Become a volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['user[fname]'] = 'Benny';
        $form['user[sname]'] = 'Borko';
        $form['user[email]'] = 'bborko@bogus.info';
        $form['user[plainPassword][first]'] = '123Abc';
        $form['user[plainPassword][second]'] = '123Abc';
        $form['user[focuses]'][0]->tick();
        $form['user[skills]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }

}

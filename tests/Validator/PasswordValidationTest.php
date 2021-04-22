<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Validator/PasswordValidationTest.php

namespace Tests\Validator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Validator
 */
class PasswordValidationTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
    }

    public function testValidPassword()
    {
        $content = $this->volunteerRegistration('123Abc');

        $this->assertStringContainsString('registration confirmation has been sent', $content);
    }

    public function testShortAlphaPassword()
    {
        $content = $this->volunteerRegistration('abc');

        $this->assertStringContainsString('At least 6 characters long', $content);
        $this->assertStringContainsString('Must include both upper and lower case letters', $content);
        $this->assertStringContainsString('Must include at least one number', $content);
    }

    public function testShortNumericPassword()
    {
        $content = $this->volunteerRegistration('123');

        $this->assertStringContainsString('At least 6 characters long', $content);
        $this->assertStringContainsString('Must include both upper and lower case letters', $content);
        $this->assertStringContainsString('Must include at least one letter', $content);
    }

    private function volunteerRegistration($password)
    {
        $this->client->request('GET', '/');
        $this->client->clickLink('Volunteer');
        $crawler = $this->client->clickLink('Become a volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['user[fname]'] = 'Benny';
        $form['user[sname]'] = 'Borko';
        $form['user[email]'] = 'bborko@bogus.info';
        $form['user[plainPassword][first]'] = $password;
        $form['user[plainPassword][second]'] = $password;
        $form['user[focuses]'][0]->tick();
        $form['user[skills]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }

}

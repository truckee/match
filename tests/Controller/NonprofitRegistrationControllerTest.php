<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/NonprofitRegistrationControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group NonprofitReg
 */
class NonprofitRegistrationControllerTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testNonprofitRegistration()
    {
        $params = [
            'ein' => '987654321',
            'email' => 'quasi@modo.org'
        ];
        $this->nonprofitRegistration($params);
        $content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('Look for the confirmation email', $content);
    }

    // Note: this test uses text from templates/Email/staff_confirmation.html.twig
    public function testNonprofitRegistrationEmail()
    {
        $params = [
            'ein' => '987654321',
            'email' => 'quasi@modo.org'
        ];
        $this->client->followRedirects(false);
        $this->nonprofitRegistration($params);

        $this->assertResponseRedirects();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);

        $this->assertStringContainsString('until the Foundation has activated the account', $email->getHtmlBody());
    }

    public function testNonprofitAlreadyRegistered()
    {
        $params = [
            'ein' => '123456789',
            'email' => 'quasi@modo.org'
        ];
        $content = $this->nonprofitRegistration($params);

        $this->assertStringContainsString('Nonprofit is already registered', $content);
    }

    public function testNonprofitEINNot9Digits()
    {
        $params = [
            'ein' => '1234789',
            'email' => 'quasi@modo.org'
        ];
        $content = $this->nonprofitRegistration($params);

        $this->assertStringContainsString('EIN has 9 digits', $content);
    }

    public function testNonprofitStaffAlreadyRegistered()
    {
        $params = [
            'ein' => '987654321',
            'email' => 'unknown@bogus.info'
        ];
        $content = $this->nonprofitRegistration($params);

        $this->assertStringContainsString('Email already registered', $content);
    }

    private function nonprofitRegistration($params)
    {
        $this->client->request('GET', '/');
        $this->client->clickLink('Nonprofits');
        $crawler = $this->client->clickLink('Register a nonprofit');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['org[orgname]'] = 'Glenshire Fund';
        $form['org[ein]'] = $params['ein'];
        $form['org[address]'] = '123 Some Street';
        $form['org[city]'] = 'SomeTown';
        $form['org[state]'] = 'ST';
        $form['org[zip]'] = '11111';
        $form['org[phone]'] = '5555551212';
        $form['org[website]'] = 'www.somesite.org';
        $form['org[rep][fname]'] = 'Benny';
        $form['org[rep][sname]'] = 'Borko';
        $form['org[rep][email]'] = $params['email'];
        $form['org[rep][plainPassword][first]'] = '123Abc';
        $form['org[rep][plainPassword][second]'] = '123Abc';
        $form['org[focuses]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }

}

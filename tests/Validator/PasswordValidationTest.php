<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Validator/PasswordValidationTest.php

namespace App\Tests\Validator;

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
        $crawler = $this->client->request('GET', '/register/volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['new_user[fname]'] = 'Benny';
        $form['new_user[sname]'] = 'Borko';
        $form['new_user[email]'] = 'bborko@bogus.info';
        $form['new_user[plainPassword][first]'] = $password;
        $form['new_user[plainPassword][second]'] = $password;
        $form['new_user[focuses]'][0]->tick();
        $form['new_user[skills]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }
}

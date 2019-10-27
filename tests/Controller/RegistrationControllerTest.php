<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/tests/Controller/RegistrationControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */
class RegistrationControllerTest extends WebTestCase
{
    public function setup() {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }
    
    public function testVolunteerPageValidation() {
        $this->client->request('GET', '/register/volunteer');
        
        $crawler = $this->client->submitForm('Submit');
        $this->assertContains('First name is required', $this->client->getResponse()->getContent());
        $this->assertContains('Last name is required', $this->client->getResponse()->getContent());
        $this->assertContains('Email is required', $this->client->getResponse()->getContent());
        $this->assertContains('Password may not empty', $this->client->getResponse()->getContent());
        $this->assertContains('At least one focus', $this->client->getResponse()->getContent());
        $this->assertContains('At least one skill', $this->client->getResponse()->getContent());
    }
    
    public function testVolunteerRegistration() {
        $this->client->request('GET', '/register/volunteer');
        $crawler = $this->client->request('GET', '/register/volunteer');
        
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['new_user[fname]'] = 'Benny';
        $form['new_user[sname]'] = 'Borko';
        $form['new_user[email]'] = 'bborko@bogus.info';
        $form['new_user[plainPassword][first]'] = '123Abc';
        $form['new_user[plainPassword][second]'] = '123Abc';
        $form['new_user[focuses][0]'] = 1;
        $form['new_user[skills][0]'] = 1;
        $this->client->submit($form);

        $this->assertContains('registration confirmation has been sent', $this->client->getResponse()->getContent());
    }
}

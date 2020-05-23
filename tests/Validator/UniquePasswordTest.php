<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/insert_path_here/UniquePasswordTest.php

namespace App\Tests\Validator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */
class UniquePasswordTest extends WebTestCase
{
    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
    }
    
    public function testAdminEmail()
    {
        $content = $this->volunteerRegistration('admin@bogus.info');
        
        $this->assertStringContainsString('Email already registered', $content);
    }
    
    public function testStaffEmail()
    {
        $content = $this->volunteerRegistration('staff@bogus.info');
        
        $this->assertStringContainsString('Email already registered', $content);
    }

    private function volunteerRegistration($email)
    {
        $crawler = $this->client->request('GET', '/register/volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['new_user[fname]'] = 'Benny';
        $form['new_user[sname]'] = 'Borko';
        $form['new_user[email]'] = $email;
        $form['new_user[plainPassword][first]'] = '123Abc';
        $form['new_user[plainPassword][second]'] = '123Abc';
        $form['new_user[focuses]'][0]->tick();
        $form['new_user[skills]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }
}

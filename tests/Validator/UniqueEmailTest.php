<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/insert_path_here/UniquePasswordTest.php

namespace Tests\Validator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Validator
 */
class UniqueEmailTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
    }

    public function testAdminEmail()
    {
        $content = $this->registration('admin@bogus.info');

        $this->assertStringContainsString('Email already registered', $content);
    }

    public function testStaffEmail()
    {
        $content = $this->registration('staff@bogus.info');

        $this->assertStringContainsString('Email already registered', $content);
    }

    private function registration($email)
    {
        $this->client->request('GET', '/');
        $this->client->clickLink('Volunteer');
        $crawler = $this->client->clickLink('Become a volunteer');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['user[fname]'] = 'Benny';
        $form['user[sname]'] = 'Borko';
        $form['user[email]'] = $email;
        $form['user[plainPassword][first]'] = '123Abc';
        $form['user[plainPassword][second]'] = '123Abc';
        $form['user[focuses]'][0]->tick();
        $form['user[skills]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }

}

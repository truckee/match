<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/OpportunityTest.php

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Opportunity
 */
class OpportunityTest extends WebTestCase
{
    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'staff@bogus.info',
            'password' => '123Abc',
        ]);
    }

    public function testAddButton()
    {
        $this->client->clickLink('Edit nonprofit profile');
        $crawler = $this->client->clickLink('Add');
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['opportunity[oppname]'] = 'Ranger';
        $form['opportunity[description]'] = 'Dorkify';
        $form['opportunity[skills]'][0]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('Opportunity added; 1', $this->client->getResponse()->getContent());
    }

    public function testEditButton()
    {
        $npoCrawler = $this->client->clickLink('Edit nonprofit profile');
        $btn = $npoCrawler->filter(".btn:contains('Edit')")->link();
        $oppCrawler = $this->client->click($btn);
        $node = $oppCrawler->selectButton('Save');
        $form = $node->form();
        $form['opportunity[oppname]'] = 'Ranger';
        $form['opportunity[description]'] = 'Dorkify';
        $form['opportunity[skills]'][1]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('Opportunity updated', $this->client->getResponse()->getContent());
    }
}

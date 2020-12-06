<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Pages/SearchTest.php

namespace App\Tests\Pages;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Pages
 */
class SearchTest extends WebTestCase
{
    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
    }
    
    public function testTargetedSearch()
    {
        $crawler = $this->client->request('GET', '/opportunity/search');
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['search[skills]'][0]->tick();
        $form['search[skills]'][1]->tick();
        $form['search[focuses]'][1]->tick();
        $form['search[focuses]'][0]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('Turkey Fund', $this->client->getResponse()->getContent());
    }
    
    public function testFocusSearch()
    {
        $crawler = $this->client->request('GET', '/opportunity/search');
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['search[focuses]'][1]->tick();
        $form['search[focuses]'][0]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('Turkey Fund', $this->client->getResponse()->getContent());
    }
    
    public function testFailedFocusSearch()
    {
        $crawler = $this->client->request('GET', '/opportunity/search');
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['search[focuses]'][0]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('No matching opportunities found', $this->client->getResponse()->getContent());
    }
    
    public function testSkillSearch()
    {
        $crawler = $this->client->request('GET', '/opportunity/search');
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['search[skills]'][0]->tick();
        $form['search[skills]'][1]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('Turkey Fund', $this->client->getResponse()->getContent());
    }
    
    public function testFailedSkillSearch()
    {
        $crawler = $this->client->request('GET', '/opportunity/search');
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['search[skills]'][1]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('No matching opportunities found', $this->client->getResponse()->getContent());
    }
    
    public function testSearch()
    {
        $this->client->request('GET', '/opportunity/search');
        $this->client->submitForm('Search');

        $this->assertStringContainsString('Turkey Fund', $this->client->getResponse()->getContent());
    }
}

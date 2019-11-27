<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/OpportunityTest.php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */
class OpportunityTest extends WebTestCase
{
    use FixturesTrait;

    public function setup(): void
    {
        $this->fixtures = $this->loadFixtures([
            'App\DataFixtures\Test\OptionsFixture',
            'App\DataFixtures\Test\NonprofitFixture',
            ])
                ->getReferenceRepository();
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'staff@bogus.info',
            'password' => '123Abc',
        ]);
    }

    public function testAddButton()
    {
        $this->client->request('GET', '/profile');
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
        $this->client->request('GET', '/profile');
        $oppId = $this->fixtures->getReference('opp')->getId();
        $crawler = $this->client->request('GET', '/opportunity/edit/' . $oppId);
        $node = $crawler->selectButton('submit');
        $form = $node->form();
        $form['opportunity[oppname]'] = 'Ranger';
        $form['opportunity[description]'] = 'Dorkify';
        $form['opportunity[skills]'][1]->tick();
        $this->client->submit($form);

        $this->assertStringContainsString('Opportunity updated', $this->client->getResponse()->getContent());
    }
}

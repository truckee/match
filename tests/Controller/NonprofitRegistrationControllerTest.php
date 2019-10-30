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
 * 
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
            'ein'=> '987654321', 
            'email'=>'quasi@modo.org'
        ];
        $this->nonprofitRegistration($params);
        $content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('Look for the confirmation email', $content);
    }
    
    public function testNonprofitRegistrationEmail()
    {
        $params = [
            'ein'=> '987654321', 
            'email'=>'quasi@modo.org'
        ];
        $this->client->enableProfiler();
        $this->client->followRedirects(false);
        $crawler = $this->client->request('GET', '/register/nonprofit');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['org[orgname]'] = 'Marmot Fund';
        $form['org[ein]'] = $params['ein'];
        $form['org[staff][fname]'] = 'Benny';
        $form['org[staff][sname]'] = 'Borko';
        $form['org[staff][email]'] = $params['email'];
        $form['org[staff][plainPassword][first]'] = '123Abc';
        $form['org[staff][plainPassword][second]'] = '123Abc';
        $form['org[focuses]'][0]->tick();
        $this->client->submit($form);
        
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());
    }
    
    public function testNonprofitAlreadyRegistered()
    {
        $params = [
            'ein'=> '123456789', 
            'email'=>'quasi@modo.org'
        ];
        $content = $this->nonprofitRegistration($params);

        $this->assertStringContainsString('Nonprofit is already registered', $content);
    }
    
    public function testNonprofitEINNot9Digits()
    {
        $params = [
            'ein'=> '1234789', 
            'email'=>'quasi@modo.org'
        ];
        $content = $this->nonprofitRegistration($params);

        $this->assertStringContainsString('EIN has 9 digits', $content);
    }
    
    public function testNonprofitStaffAlreadyRegistered()
    {
        $params = [
            'ein'=> '987654321', 
            'email'=>'unknown@bogus.info'
        ];
        $content = $this->nonprofitRegistration($params);

        $this->assertStringContainsString('Email already registered', $content);
    }
    
    private function nonprofitRegistration($params)
    {
        $crawler = $this->client->request('GET', '/register/nonprofit');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['org[orgname]'] = 'Marmot Fund';
        $form['org[ein]'] = $params['ein'];
        $form['org[staff][fname]'] = 'Benny';
        $form['org[staff][sname]'] = 'Borko';
        $form['org[staff][email]'] = $params['email'];
        $form['org[staff][plainPassword][first]'] = '123Abc';
        $form['org[staff][plainPassword][second]'] = '123Abc';
        $form['org[focuses]'][0]->tick();
        $this->client->submit($form);

        return $this->client->getResponse()->getContent();
    }

}

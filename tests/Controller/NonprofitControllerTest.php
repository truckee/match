<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Controller/NonprofitControllerTest.php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @group Nonprofit
 */
class NonprofitControllerTest extends WebTestCase
{

    public function setup(): void
    {
        $this->client = $this->createClient();
        $this->client->followRedirects();
    }

    public function testView()
    {
        $this->client->request('GET', '/');
        $this->client->clickLink('Volunteer');
        $this->client->clickLink('Search for opportunities');
        $this->client->submitForm('submit');

        $this->assertStringContainsString('Turkey Fund', $this->client->getResponse()->getContent());
    }

//
//    /**
//     * @expectedException
//     */
//    public function testNonprofitNotFound()
//    {
//        $this->expectExceptionMessage('Nonprofit object not found');
//
//        $this->client->request('GET', '/nonprofit/view/0');
//    }
}

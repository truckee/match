<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Services/OppEmailTest.php

namespace App\Tests\Services;

use App\Entity\Opportunity;
use App\Entity\Person;
use App\Services\NewOppEmailService;
use App\Services\EmailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group Services
 */
class OppEmailTest extends TestCase
{

    public function testOppEmail()
    {
        $vol1 = new Person();
        $vol1->setFname('A');
        $vol1->setEmail('A@b.com');
        $vol2 = new Person();
        $vol2->setFname('B');
        $vol2->setEmail('b@c.com');
        $vol3 = new Person();
        $vol3->setFname('C');
        $vol3->setEmail('C@d.com');
        $volunteers = [$vol1, $vol2, $vol3];
        $opp = new Opportunity();
        $opp->getOppname('D');
        $mailer = $this->createMock(EmailerService::class);

        $repo = $this->createMock(ObjectRepository::class);
        $repo->expects($this->any())
                ->method('find')
                ->willReturn($vol1);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())
                ->method('getRepository')
                ->willReturn($repo);

        $oppEmail = new NewOppEmailService($objectManager, 'e@f.com');
        $oppVols = $oppEmail->newOppEmail($mailer, $volunteers, $opp);
        $this->assertIsArray($oppVols);

        $keys = array_keys($oppVols);
        $this->assertEquals(3, count($oppVols[$keys[0]]));
    }

}

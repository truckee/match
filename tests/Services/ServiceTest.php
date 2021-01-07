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
use App\Services\EmailerService;
use App\Services\NewOppEmailService;
use App\Services\PersonService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group Services
 */
class ServiceTest extends TestCase
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
        $adm = new Person();
        $adm->setEmail('q@anon.com');
        $volunteers = [$vol1, $vol2, $vol3];
        $opp = new Opportunity();
        $opp->getOppname('D');
        $mailer = $this->createMock(EmailerService::class);

        $repo = $this->createMock(ObjectRepository::class);
        $repo->expects($this->any())
                ->method('find')
                ->willReturn($vol1);
        $repo->expects($this->once())
                ->method('findOneBy')
                ->willReturn($adm);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())
                ->method('getRepository')
                ->willReturn($repo);

        $oppEmail = new NewOppEmailService($entityManager, 'e@f.com');
        $oppVols = $oppEmail->newOppEmail($mailer, $volunteers, $opp);
        $this->assertIsArray($oppVols);

        $keys = array_keys($oppVols);
        $this->assertEquals(3, count($oppVols[$keys[0]]));
    }

    public function testSwitchMailer()
    {
        /*
         * Cases:
         *      $admin1 : $id = 1; $mailer = true; $enabled = true;
         *      $admin2 : $id = 2; $mailer = false; $enabled = true;
         *      $admin3 : $id = 3; $mailer = false; $enabled = false;
         * Repo method calls:
         *      findOneBy(): always return $adm1
         *      find: twice, returns $adm2, $adm3
         *      findBy: twice, returns [$adm1, $adm2]
         */
        $adm1 = new Person();
        $adm1->setMailer(true);
        $adm1->setEnabled(true);
        $adm2 = new Person();
        $adm2->setMailer(false);
        $adm2->setEnabled(true);
        $adm3 = new Person();
        $adm3->setMailer(false);
        $adm3->setEnabled(false);
        $var = 'adm';
        for ($i = 1; $i < 4; $i++) {
            $name = $var . $i;
            $refClass = new \ReflectionClass($$name);
            $idProperty = $refClass->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($$name, $i);
        }

        $repo = $this->createMock(ObjectRepository::class);
        $repo->expects($this->any())
                ->method('findOneBy')
                ->willReturn($adm1);
        $repo->expects($this->exactly(2))
                ->method('find')
                ->will($this->onConsecutiveCalls($adm2, $adm3));
        $repo->expects($this->any())
                ->method('findBy')
                ->willReturn([$adm1, $adm3]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())
                ->method('getRepository')
                ->willReturn($repo);

        $svc = new PersonService($entityManager);

        $original = $svc->switchFns('Admin', '1', 'mailer');
        $new = $svc->switchFns('Admin', '2', 'mailer');
        $failed = $svc->switchFns('Admin', '3', 'mailer');

        $this->assertEquals(['type' => 'info', 'content' => 'No change made'], $original);
        $this->assertEquals(['type' => 'success', 'content' => 'Mailer status changed'], $new);
        $this->assertEquals(['type' => 'warning', 'content' => 'Disabled admins cannot be mailer'], $failed);
    }

}

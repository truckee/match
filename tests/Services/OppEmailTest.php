<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Services/OppEmailTest.php

namespace App\Tests\Services;

use App\Entity\NewOppEmail;
use App\Services\NewOppEmailService;
//use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

/**
 * 
 */
class OppEmailTest extends TestCase
{
    public function testEmptyOppEmail()
    {
        $oppEmail = new NewOppEmail();
        $oppEmail->setVolunteerEmail([]);
        
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($oppEmail);
        
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);
        
        $templating = $this->createMock(EngineInterface::class);
        
        $service = new NewOppEmailService($objectManager, $templating);
        
        $newList = $service->addToList([1, 2], 10);
        $this->assertEquals(2, count(array_keys($newList)));
        $this->assertTrue(is_array($newList[1]));
        $this->assertTrue(in_array(10, $newList[1]));
    }

        public function testNonEmptyOppEmail()
    {
        $oppEmail = new NewOppEmail();
        $oppEmail->setVolunteerEmail([1=>[10, 12], 2=>[10,14]]);
        
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($oppEmail);
        
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);
        
        $service = new NewOppEmailService($objectManager);
        
        $newList = $service->addToList([1, 3], 16);
        $this->assertEquals(3, count(array_keys($newList)));
        $this->assertEquals(3, count($newList[1]));
        $this->assertEquals(2, count($newList[2]));
        $this->assertEquals(1, count($newList[3]));
        $this->assertTrue(in_array(16, $newList[3]));
    }

    }

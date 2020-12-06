<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Command/ExpiringOppsTest.php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 *
 */
class ExpiringOppsTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:send:expiringopps');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command'  => $command->getName(),]);
        $output = $commandTester->getDisplay();
        
        $this->assertStringContainsString('', $output);
    }
}

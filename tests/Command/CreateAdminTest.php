<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests/Command/CreateAdminTest.php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 *
 */
class CreateAdminTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:create-admin');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // pass arguments to the helper
            '--fname' => 'bowling',
            '--sname' => 'ball',
            '--email' => 'bball@bogus.info',
            '--password' => '123Abc',
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Admin user created', $output);
    }
}

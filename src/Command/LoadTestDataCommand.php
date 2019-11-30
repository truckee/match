<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/insert_path_here/LoadTestDataCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * 
 */
class LoadTestDataCommand extends Command
{

    protected static $defaultName = 'app:load:test';

    protected function configure()
    {
        $this->setDescription('Drops, creates database; creates schema; loads test fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command1 = $this->getApplication()->find('doctrine:database:drop');
        $arguments1 = [
            'command' => 'doctrine:database:drop',
            '--force' => true,
            ];
        $drop = new ArrayInput($arguments1);
        $command1->run($drop, $output);
        
        $command2 = $this->getApplication()->find('doctrine:database:create');
        $arguments2 = ['command' => 'doctrine:database:create'];
        $createDb = new ArrayInput($arguments2);
        $command2->run($createDb, $output);
        
        $command3 = $this->getApplication()->find('doctrine:schema:create');
        $arguments3 = ['command' => 'doctrine:schema:create'];
        $createSchema = new ArrayInput($arguments3);
        $command3->run($createSchema, $output);
        
        $command4 = $this->getApplication()->find('doctrine:fixtures:load');
        $arguments4 = ['command' => 'doctrine:fixtures:load'];
        $loadFixtures = new ArrayInput($arguments4);
        $command4->run($loadFixtures, $output);
    }

}

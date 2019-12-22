<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Command/SendOppEmailCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use App\Services\EmailerService;
use App\Services\NewOppEmailService;
use Twig\Environment;

/**
 * 
 */
class SendOppEmailCommand extends Command
{
    private $mailer;
    private $sender;
    private $twig;
    
    public function __construct(NewOppEmailService $sender, EmailerService $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->twig = $twig;
        
        parent::__construct();
    }
    
    protected static $defaultName = 'app:send:oppmail';
    
    protected function configure()
    {
        $this->setDescription('Sends spooled new opportunities email to selected volunteers');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sender->sendList($this->mailer, $this->twig);
        
        $command = $this->getApplication()->find('swiftmailer:spool:send');
        $arguments = ['command' => 'swiftmailer:spool:send',];
        $mailed = new ArrayInput($arguments);
        $command->run($mailed, $output);

        return 0;
    }
}

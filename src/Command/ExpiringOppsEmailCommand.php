<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Command/ExpiringOppsEmailCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use App\Services\EmailerService;
use App\Services\ExpirationNoticeService;
use Twig\Environment;

/**
 * 
 */
class ExpiringOppsEmailCommand extends Command
{
    private $mailer;
    private $sender;
    private $twig;
    
    public function __construct(ExpirationNoticeService $sender, EmailerService $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->twig = $twig;
        
        parent::__construct();
    }
    
    protected static $defaultName = 'app:send:expiringopps';
    
    protected function configure()
    {
        $this->setDescription('Sends email re: expiring opps to nonprofit staff email');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sender->expirationNotices($this->mailer, $this->twig);
    }
}

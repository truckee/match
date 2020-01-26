<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Command/CreateAdminCommand.php

namespace App\Command;

use App\Services\AdminManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{

    protected static $defaultName = 'app:create-admin';
    private $em;
    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this
                ->setHidden(true)
                ->setName('app:create-admin')
                ->setDescription('Creates a new admin user.')
                ->setHelp('Used only in installation script; only creates an admin user. '
                        . 'Volunteers & nonprofit staff '
                        . 'must register via the website.')
                ->setDefinition([
                    new InputOption('fname', '', InputOption::VALUE_OPTIONAL, 'First name'),
                    new InputOption('sname', '', InputOption::VALUE_OPTIONAL, ' name'),
                    new InputOption('email', '', InputOption::VALUE_OPTIONAL, 'Email'),
                    new InputOption('password', '', InputOption::VALUE_OPTIONAL, 'password'),
                ])
        ;
//        $this
//                ->addArgument('fname', InputArgument::OPTIONAL, 'First name')
//                ->addArgument('sname', InputArgument::OPTIONAL, 'Last name')
//                ->addArgument('email', InputArgument::OPTIONAL, 'Email')
//                ->addArgument('password', InputArgument::OPTIONAL, 'Password')
//                ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        if (!$input->getOption('fname')) {
            $question = new Question('First name: ', '');
            $fname = $helper->ask($input, $output, $question);
            if (empty($fname)) {
                throw new \Exception('First name may not be empty');
            }
            $input->setOption('fname', $fname);
        } 
//        else {
//            $input->setOption('fname', $input->getArgument('fname'));
//        }

        if (!$input->getOption('sname')) {
            $question = new Question('Last name: ', '');
            $sname = $helper->ask($input, $output, $question);
            if (empty($fname)) {
                throw new \Exception('Last name may not be empty');
            }
            $input->setOption('sname', $sname);
        } 
//        else {
//            $input->setOption('sname', $input->getArgument('sname'));
//        }

        if (!$input->getOption('email')) {
            $question = new Question('Email: ', '');
            $email = $helper->ask($input, $output, $question);
            if (empty($email)) {
                throw new \Exception('Email may not be empty');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Not a valid email address');
            }
            $input->setOption('email', $email);
        } 
//        else {
//            $input->setOption('email', $input->getArgument('email'));
//        }

        if (!$input->getOption('password')) {
            $question = new Question('Password: ', '');
            $password = $helper->ask($input, $output, $question);
            if (empty($password)) {
                throw new \Exception('Password may not be empty');
            }
            $input->setOption('password', $password);
        } 
//        else {
//            $input->setOption('password', $input->getArgument('password'));
//        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new AdminManager($this->em, $this->encoder);
        $admin = $manager->createAdmin($input->getOptions());
        if (is_object($admin)) {
             $output->writeln('Admin user created');
        } else {
            $output->writeln('Something went wrong!');
        }
        
        return 0;
    }

}

<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/DefaultController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\NewOppEmailService;
use App\Services\EmailerService;
use Twig\Environment;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('Default/home.html.twig');
    }
    
    /**
     * @Route("/sender", name = "opp_sender")
     */
    public function sendStuff(NewOppEmailService $sender, Environment $templating, EmailerService $mailer)
    {
        $sender->sendList($mailer, $templating);
        
        return $this->redirectToRoute('home');
    }
}

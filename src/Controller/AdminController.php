<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/Admin.php

namespace App\Controller;

use App\Entity\Nonprofit;
use App\Services\Emailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/", name="admin")
     * 
     */
    public function index()
    {
        return $this->render('Admin/index.html.twig');
    }

    /**
     * @Route("/activate/{ein}", name="activate_nonprofit")
     * 
     */
    public function activate(Emailer $mailer, $ein = null)
    {
        $em = $this->getDoctrine()->getManager();
        $npo = $em->getRepository(Nonprofit::class)->findOneBy(['ein' => $ein]);
        if (null === $npo) {
            $this->addFlash(
                    'warning',
                    'Nonprofit not found'
            );

            return $this->redirectToRoute('admin');
        }

        $npo->setTemp(false);
        $em->persist($npo);
        $em->flush();

        $view = $this->renderView('Email/nonprofit_activated.html.twig', [
            'orgname' => $npo->getOrgname(),
        ]);
        $mailParams = [
            'view' => $view,
            'recipient' => $npo->getStaff()->getEmail(),
            'subject' => 'Nonprofit activated!',
        ];
        $mailer->appMailer($mailParams);


        $this->addFlash(
                'success',
                'It worked?!'
        );

        return $this->redirectToRoute('admin');
    }

}

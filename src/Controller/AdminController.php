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

        $npo->setActive(true);
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

//    /**
//     * Use only for testing spooling of email
//     * 
//     * @Route("/spool", name = "spool_test")
//     */
//    public function spool(Emailer $mailer)
////    {
//        $mailParams = [
//            'view' => $this->renderView('Email/nonprofit_activated.html.twig', [
//                'orgname' => 'Vader Enterprises',
//            ]),
//            'recipient' => 'developer@bogus.info',
//            'subject' => 'Spool test',
//            'spool' => true,
//        ];
//        $mailer->appMailer($mailParams);
//
//        return $this->redirectToRoute('admin');
//    }

}

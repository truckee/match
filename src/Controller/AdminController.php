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
use App\Services\EmailerService;
use App\Services\ChartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/", name="admin")
     * 
     */
    public function index(ChartService $charter)
    {
        $volChart = $charter->volunteerChart();
        $searchGauge = $charter->searchGauge();
        $focus = $charter->sankeyFocus();

        return $this->render('Admin/index.html.twig', [
                    'vol_chart' => $volChart,
                    'search_gauge' => $searchGauge,
                    'focus' => $focus
        ]);
    }

    /**
     * @Route("/activate/{ein}", name="activate_nonprofit")
     * 
     */
    public function activate(EmailerService $mailer, $ein = null)
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
                'Nonprofit activated!'
        );

        return $this->redirectToRoute('admin');
    }

//    /**
//     * Use only for testing spooling of email
//     * 
//     * @Route("/spool", name = "spool_test")
//     */
//    public function spool(EmailerService $mailer)
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

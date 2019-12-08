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
use App\Entity\User;
use App\Entity\Volunteer;
use App\Services\EmailerService;
use App\Services\ChartService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

/**
 * @Route("/admin")
 */
class AdminController extends EasyAdminController
{

    /**
     * @Route("/dashboard", name="dashboard")
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
     * @Route("/status/{id}", name="status")
     *
     */
    public function statusChange(Request $request, EmailerService $mailer, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $npo = $em->getRepository(Nonprofit::class)->find($id);
        if (null === $npo) {
            $this->addFlash(
                    'warning',
                    'Nonprofit not found'
            );

            return $this->redirectToRoute('dashboard');
        }

        $status = $npo->isActive();
        $npo->setActive(!$status);
        $staff = $npo->getStaff();

        // activate if $status is false
        if (false === $status) {
            $staff->setLocked(false);
            $view = $this->renderView('Email/nonprofit_activated.html.twig', [
                'npo' => $npo,
                'staff' => $npo->getStaff(),
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
        } else {
            $npo->setActive(false);
            $staff->setLocked(true);
            $this->addFlash(
                    'success',
                    'Nonprofit deactivated; staff account locked'
            );
        }
        $em->persist($npo);
        $em->persist($staff);
        $em->flush();


        $route = $request->query->get('route');
        if (null !== $route) {
            return $this->redirectToRoute('easyadmin', ['entity' => 'Nonprofit']);
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/lock/{id}", name = "lock_user")
     */
    public function lockUser($id)
    {
        if (null === $id) {
            return;
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $state = $user->isLocked();
        $user->setLocked(!$state);
        $em->persist($user);
        $em->flush();
        if (Volunteer::class === get_class($user)) {
            $entity = 'Volunteer';
        } else {
            $entity = 'Staff';
        }
        

        return $this->redirectToRoute('easyadmin', ['entity' => $entity]);
    }

    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        /* @var EntityManager */
        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);

        /* @var QueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
                ->select('entity')
                ->from($this->entity['class'], 'entity')
        ;

        if (!empty($dqlFilter)) {
            $queryBuilder->andWhere($dqlFilter);
        }

        if (Volunteer::class === $this->entity['class']) {
            $queryBuilder->addOrderBy('entity.sname', 'ASC');
            $queryBuilder->addOrderBy('entity.fname', 'ASC');
            $queryBuilder->addOrderBy('entity.email', 'ASC');
        }

        return $queryBuilder;
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
//        return $this->redirectToRoute('dashboard');
//    }
}

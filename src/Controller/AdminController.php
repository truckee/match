<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/Admin.php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Nonprofit;
use App\Entity\Staff;
use App\Entity\User;
use App\Entity\Volunteer;
use App\Form\Type\UserType;
use App\Services\EmailerService;
use App\Services\ChartService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Activates or deactivates a nonprofit
     * 
     * @Route("/status/{id}", name="status")
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
            $staff->setEnabled(true);
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
        if (Volunteer::class === get_class($user)) {
            $entity = 'Volunteer';
        } else {
            $entity = 'Staff';
            $nonprofit = $user->getNonprofit();
            $nonprofit->setActive(false);
            $em->persist($nonprofit);
        }
        $em->flush();
        $lockState = $user->isLocked() ? ' is now locked' : ' is now unlocked';
        $this->addFlash('success', $user->getFullName() . $lockState);

        return $this->redirectToRoute('easyadmin', ['entity' => $entity]);
    }

    // provides sort ordering for entity display
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        /* @var EntityManager */
        $em = $this->getDoctrine()->getManagerForClass($entityClass);

        /* @var QueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
                ->select('entity')
                ->from($entityClass, 'entity')
        ;

        if (!empty($dqlFilter)) {
            $queryBuilder->andWhere($dqlFilter);
        }

        if (Volunteer::class === $entityClass || Staff::class === $entityClass) {
            $queryBuilder->addOrderBy('entity.sname', 'ASC');
            $queryBuilder->addOrderBy('entity.fname', 'ASC');
            $queryBuilder->addOrderBy('entity.email', 'ASC');
        }

        return $queryBuilder;
    }

    /**
     * @Route("/replaceStaff/{id}", name="replace_staff")
     */
    public function replaceStaff(Request $request, EmailerService $mailer, $id)
    {
        if (null === $id) {
            return;
        }

        $replacement = new Staff();
        $em = $this->getDoctrine()->getManager();
        $staff = $em->getRepository(Staff::class)->find($id);
        $nonprofit = $staff->getNonprofit();
        $form = $this->createForm(UserType::class, $replacement, [
            'data_class' => Staff::class,
            'npo_id' => $nonprofit->getId(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('user')['email'];
            $id = $request->request->get('user')['npoid'];
            $token = md5(uniqid(rand(), true));
            $expiresAt = new \DateTime();
            $view = $this->renderView('Email/staff_replacement.html.twig', [
                'replacement' => $replacement,
                'nonprofit' => $nonprofit,
                'token' => $token,
                'expires' => $expiresAt,
            ]);
            $mailParams = [
                'view' => $view,
                'recipient' => $email,
                'subject' => $nonprofit->getOrgname() . ' staff replacement',
            ];
            $mailer->appMailer($mailParams);

            $replacement->setConfirmationToken($token);
            $replacement->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
            $replacement->setReplacementOrg($id);
            // password is required but never used
            $replacement->setPassword('hailhail');
            $replacement->setEnabled(false);
            $em->persist($replacement);
            $em->flush();

            $this->addFlash('success', 'Replacement email sent');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => [
                        'Default/_empty.html.twig',
                        'Profile/_user.html.twig'],
                    'staff' => $staff,
                    'headerText' => 'Replacement for ' . $staff->getFullName() . '<br />' .
                    $staff->getNonprofit()->getOrgname(),
                ])
        ;
    }

    /**
     * Invitation creates a new admin without a password
     *
     * @Route("/invite", name="admin_invite")
     */
    public function invite(Request $request, EmailerService $mailer)
    {
        $admin = new Admin();
        $form = $this->createForm(UserType::class, $admin, [
            'data_class' => User::class
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
//            $invite = new Invitation();
            $admin->setConfirmationToken(md5(uniqid(rand(), true)));
            $admin->setEmail($admin->getEmail());
            $admin->setFname($admin->getFname());
            $admin->setSname($admin->getSname());
            $expiresAt = new \DateTime();
            $admin->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
            
            $view = $this->renderView('Email/staff_replacement.html.twig', [
                'replacement' => $replacement,
                'nonprofit' => $nonprofit,
                'token' => $token,
                'expires' => $expiresAt,
            ]);
            $mailParams = [
                'view' => $view,
                'recipient' => $email,
                'subject' => $nonprofit->getOrgname() . ' staff replacement',
            ];
            $mailer->appMailer($mailParams);
        }
        $templates = [
            'Default/_empty.html.twig',
            'Registration/_new_user.html.twig',
        ];
        
        return $this->render('Default/form_templates.html.twig', [
            'form' => $form->createView(),
            'headerText' => 'Invite new admin user',
            'userHeader' => '',
            'templates' => $templates,
            'invite' => true,
        ]);
    }
    
    /**
     * @Route("/assign/{id}", name="assign_activator")
     */
    public function assign($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Admin::class)->findAll();
        foreach ($entities as $admin) {
            if ((int) $id === $admin->getId()) {
                $admin->setActivator(true);
                $em->persist($admin);
            } else {
                $admin->setActivator(false);
                $em->persist($admin);
            }
        }
        $em->flush();
       
        $response = new JsonResponse(json_encode($id));
        return $response;
    }
}

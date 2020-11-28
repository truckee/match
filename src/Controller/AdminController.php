<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/Admin.php

namespace App\Controller;

use App\Controller\Admin\NonprofitCrudController;
use App\Entity\Nonprofit;
use App\Entity\Person;
use App\Form\Type\UserType;
use App\Services\EmailerService;
use App\Services\ChartService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    private $crudUrlGenerator;

    public function __construct(CrudUrlGenerator $crudUrlGenerator)
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

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
        $rep = $em->getRepository(Person::class)->findOneBy(['nonprofit' => $npo, 'replacementStatus' => 'Replace']);

        // activate staff if $status is false
        if (false === $status) {
            $rep->setLocked(false);
            $rep->setEnabled(true);
            $view = $this->renderView('Email/nonprofit_activated.html.twig', [
                'npo' => $npo,
                'staff' => $rep,
            ]);
            $mailParams = [
                'view' => $view,
                'recipient' => $rep->getEmail(),
                'subject' => 'Nonprofit activated!',
            ];
            $mailer->appMailer($mailParams);

            $this->addFlash(
                    'success',
                    'Nonprofit activated!'
            );
        } else {
            $npo->setActive(false);
            $rep->setLocked(true);
            $this->addFlash(
                    'success',
                    'Nonprofit deactivated; staff account locked'
            );
        }
        $em->persist($npo);
        $em->persist($rep);
        $em->flush();

        $url = $this->crudUrlGenerator
                ->build()
                ->setController(NonprofitCrudController::class)
                ->setAction(Action::INDEX);

        return $this->redirect($url);
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
        $user = $em->getRepository(Person::class)->find($id);
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

    /**
     * @Route("/replaceStaff/{id}", name="replace_staff")
     */
    public function replaceStaff(Request $request, EmailerService $mailer, $id)
    {
        if (null === $id) {
            return;
        }

        $replacement = new Person();
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(Representative::class)->find($id);
        $nonprofit = $rep->getNonprofit();
        $form = $this->createForm(UserType::class, $replacement, [
            'data_class' => Person::class,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('user')['email'];
            $token = md5(uniqid(rand(), true));
            $expiresAt = date_add(new \DateTime(), new \DateInterval('P7D'));

            $replacement->setTokenExpiresAt($expiresAt);
            $replacement->setConfirmationToken($token);
            $replacement->setPassword('hailhail');
            $replacement->setEnabled(false);
            $replacement->setNonprofit($nonprofit);
            $replacement->setReplacementStatus('Replacement');
            $replacement->setInitiated(new \DateTime());

            $rep->setReplacementStatus('Pending');

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

            $em->persist($rep);
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
                    'staff' => $rep,
                    'headerText' => 'Replacement for ' . $rep->getFullName() . '<br />' .
                    $rep->getNonprofit()->getOrgname(),
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
        $admin = new Person();
        $form = $this->createForm(UserType::class, $admin, [
            'data_class' => Person::class
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $admin->setMailer(false);
            $token = md5(uniqid(rand(), true));
            $admin->setConfirmationToken($token);
            $admin->setEmail($admin->getEmail());
            $admin->setEnabled(false);
//            $admin->setFname($admin->getFname());
//            $admin->setSname($admin->getSname());
            $expiresAt = new \DateTime();
            $admin->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
            // mandatory password never validated
            $admin->setPassword('new_admin');
            $admin->addRole('ROLE_ADMIN');
            $em->persist($admin);
            $em->flush();

            $view = $this->renderView('Email/invitation.html.twig', [
                'fname' => $admin->getFname(),
                'token' => $token,
                'expires' => $expiresAt,
            ]);
            $mailParams = [
                'view' => $view,
                'recipient' => $admin->getEmail(),
                'subject' => 'Invitation from ConnectionsReno',
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

//
//    /**
//     * @Route("/assign/{id}", name="assign_mailer")
//     */
//    public function assign($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository(Admin::class)->findAll();
//        foreach ($entities as $admin) {
//            if ((int) $id === $admin->getId()) {
//                $admin->setMailer(true);
//                $em->persist($admin);
//            } else {
//                $admin->setMailer(false);
//                $em->persist($admin);
//            }
//        }
//        $em->flush();
//
//        $response = new JsonResponse(json_encode($id));
//        return $response;
//    }
//
//    /**
//     * @Route("/enabler", name = "admin_enabler")
//     */
//    public function enabler(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $id = $request->query->get('id');
//        $admin = $em->getRepository(Admin::class)->find($id);
//        $enabled = $admin->isEnabled();
//        if (!$admin->getMailer() && !$admin->hasRole('ROLE_SUPER_ADMIN')) {
//            $admin->setEnabled(!$enabled);
//            $em->persist($admin);
//            $em->flush();
//        } else {
//            $this->addFlash('danger', $admin->getFullName() . ' cannot be disabled');
//        }
//
//        return $this->redirectToRoute('easyadmin', array(
//            'action' => 'list',
//            'entity' => $request->query->get('entity'),
//        ));    }
}

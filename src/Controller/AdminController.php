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
use App\Entity\Focus;
use App\Entity\Nonprofit;
use App\Entity\Representative;
use App\Entity\Skill;
use App\Entity\User;
use App\Entity\Volunteer;
use App\Form\Type\UserType;
use App\Services\EmailerService;
//use App\Services\ChartService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\JsonResponse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

/**
 * @Route("/admin")
 */
class AdminController extends EasyAdminController
{

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
        $rep = $em->getRepository(Representative::class)->findOneBy(['nonprofit' => $npo, 'replacementStatus' => 'Replace']);

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


        $route = $request->query->get('route');
        if (null !== $route) {
            return $this->redirect($route);
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/lock/{id}", name = "lock_user")
     */
    public function lockUser(Request $request, $id)
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

        return $this->redirect($request->headers->get('referer'));
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

        if (Volunteer::class === $entityClass) {
            $queryBuilder->addOrderBy('entity.sname', 'ASC');
            $queryBuilder->addOrderBy('entity.fname', 'ASC');
            $queryBuilder->addOrderBy('entity.email', 'ASC');
        }

        if (Representative::class === $entityClass) {
            $queryBuilder->addOrderBy('entity.nonprofit', 'ASC');
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

        $replacement = new Representative();
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(Representative::class)->find($id);
        $nonprofit = $rep->getNonprofit();
        $form = $this->createForm(UserType::class, $replacement, [
            'data_class' => Representative::class,
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

            return $this->redirect($request->headers->get('referer'));
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
        $admin = new Admin();
        $form = $this->createForm(UserType::class, $admin, [
            'data_class' => Admin::class
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $admin->setMailer(false);
            $token = md5(uniqid(rand(), true));
            $admin->setConfirmationToken($token);
            $admin->setEmail($admin->getEmail());
            $admin->setEnabled(false);
            $admin->setFname($admin->getFname());
            $admin->setSname($admin->getSname());
            $expiresAt = new \DateTime();
            $admin->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
            // mandatory password never validated
            $admin->setPassword('new_admin');
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

    /**
     * @Route("/switch/{class}/{id}/{field}", name="admin_switch")
     */
    public function switch(Request $request, $class, $id, $field)
    {
        switch ($class):
            case 'Admin':
                if ('mailer' === $field) {
                    $this->mailer($id);
                }
        if ('enabled' === $field) {
            $this->adminEnabler($id);
        }
        break;
        default:
                $this->enabler($class, $field, $id);
        break;
        endswitch;

        return $this->redirect($request->headers->get('referer'));
    }

    private function mailer($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mailer = $em->getRepository(Admin::class)->findOneBy(['mailer' => true]);
        
        if ((int) $id === $mailer->getId()) {
            return;
        }
        
        $selected = $em->getRepository(Admin::class)->find($id);
        if (false === $selected->getEnabled()) {
            $this->addFlash('warning', 'Disabled admins cannot be mailer');

            return;
        }
        
        $entities = $em->getRepository(Admin::class)->findBy(['enabled' => true]);
        foreach ($entities as $admin) {
            if ((int) $id === $admin->getId()) {
                $admin->setMailer(true);
            } else {
                $admin->setMailer(false);
            }
            $em->persist($admin);
        }
        $em->flush();
    }

    private function enabler($class, $field, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $vol = $em->getRepository('App\\Entity\\' . $class)->find($id);
        $getter = 'get' . ucfirst($field);
        $setter = 'set' . ucfirst($field);
        $value = $vol->$getter();
        $vol->$setter(!$value);
        $em->persist($vol);
        $em->flush();
    }

    private function adminEnabler($id)
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $em->getRepository(Admin::class)->find($id);
        $enabled = $admin->getEnabled();
        if (!$admin->isMailer() && !$admin->hasRole('ROLE_SUPER_ADMIN')) {
            $admin->setEnabled(!$enabled);
            $em->persist($admin);
            $em->flush();
        } else {
            $this->addFlash('danger', $admin->getFullName() . ' cannot be disabled');
        }
    }
}

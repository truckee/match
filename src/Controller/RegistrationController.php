<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Nonprofit;
use App\Entity\Staff;
use App\Entity\Volunteer;
use App\Form\Type\NonprofitType;
use App\Form\Type\NewUserType;
use App\Form\Type\NewPasswordType;
use App\Form\Type\Field\UserEmailType;
use App\Services\EmailerService;
use App\Security\TokenChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register")
 */
class RegistrationController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Render a form to submit email address
     *
     * @Route("/forgot", name="register_forgot")
     */
    public function forgotPassword(Request $request, EmailerService $mailer)
    {
        $form = $this->createForm(UserEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('user_email')['email'];
            $em = $this->getDoctrine()->getManager();
            $sender = $this->getParameter('app.sender_address');
            $user = $em->getRepository('App:User')->findOneBy(['email' => $email]);
            $this->addFlash(
                'success',
                'Email sent to address provided'
            );

            // if nonUser
            if (null === $user) {
                $view = $this->renderView('Email/non_user_forgotten_password.html.twig', [
                    'supportEmail' => $sender
                ]);
            } else {
                $token = md5(uniqid(rand(), true));
                $user->setConfirmationToken($token);
                $expiresAt = new \DateTime();
                $user->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));

                $em->persist($user);
                $em->flush();
                $view = $this->renderView('Email/forgotten.html.twig', [
                    'fname' => $user->getFname(),
                    'token' => $token,
                    'expiresAt' => $expiresAt,
                ]);
            }

            $mailParams = [
                'view' => $view,
                'recipient' => $email,
                'subject' => 'Volunteer Connections forgotten password',
            ];
            $mailer->appMailer($mailParams);

            return $this->redirectToRoute('home');
        }

        return $this->render('Registration/forgot.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Request forgotten password form'
        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset_password")
     */
    public function resetPassword(Request $request, TokenChecker $checker, UserPasswordEncoderInterface $passwordEncoder, $token = null)
    {
        $user = $checker->checkToken($token);
        if (null === $user) {
            return $this->redirectToRoute('home');
        }

        $em = $this->getDoctrine()->getManager();
        $expiresAt = $user->getTokenExpiresAt();
        $now = new \DateTime();
        // has token expired?
        if ($now > $expiresAt) {
            $this->addFlash(
                'danger',
                'Password forgotten link has expired'
            );

            return $this->redirectToRoute('home');
        }

        $templates = [
            'Default/_empty.html.twig',
            'Registration/_password.html.twig',
        ];
        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
            );
            if (Staff::class === get_class($user) && null !== $user->getReplacementOrg()) {
                $id = $user->getReplacementOrg();
                $nonprofit = $em->getRepository(Nonprofit::class)->find($id);
                $staff = $nonprofit->getStaff();
                $nonprofit->setStaff($user);
                $user->setNonprofit($nonprofit);
                $em->persist($nonprofit);
                $em->remove($staff);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Your password has been updated'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Set new password',
                    'templates' => $templates,
        ]);
    }

    /**
     * @Route("/volunteer", name="register_volunteer")
     */
    public function registerVolunteer(Request $request, EmailerService $mailer)
    {
        $volunteer = new Volunteer();
        $form = $this->createForm(NewUserType::class, $volunteer, [
            'register' => true,
            'data_class' => Volunteer::class,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $request->request->get('new_user');
            $this->volunteerProperties($volunteer, $userData);

            // send confirmation email
            $view = $this->renderView('Email/volunteer_confirmation.html.twig', [
                'fname' => $volunteer->getFname(),
                'token' => $volunteer->getConfirmationToken(),
                'expires' => $volunteer->getTokenExpiresAt(),
            ]);
            $mailParams = [
                'view' => $view,
                'recipient' => $volunteer->getEmail(),
                'subject' => 'Volunteer Connections',
            ];
            $mailer->appMailer($mailParams);

            $em = $this->getDoctrine()->getManager();
            $em->persist($volunteer);
            $em->flush();
            $this->addFlash(
                'success',
                'A volunteer registration confirmation has been sent to your email address'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Volunteer/volunteer_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Become a volunteer',
                    'userHeader' => 'Volunteer',
                    'focusHeader' => "Volunteer's Focus(es)",
                    'skillHeader' => "Volunteer's Skill(s)",
//                    'templates' => $templates,
        ]);
    }

    /**
     * @Route("/nonprofit", name="register_org")
     */
    public function registerNonprofit(Request $request, EmailerService $mailer)
    {
        $org = new Nonprofit();
        $form = $this->createForm(NonprofitType::class, $org, ['register' => true,]);
        $templates = [
            'Nonprofit/_nonprofit_form.html.twig',
            'Registration/_new_user.html.twig',
            'Default/_focuses.html.twig',
        ];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orgData = $request->request->get('org');
            $em = $this->getDoctrine()->getManager();
            $staff = $this->staffProperties($orgData['staff']);
            $org->setStaff($staff);
            $org->setActive(false);
            // send confirmation email
            $view = $this->renderView('Email/staff_confirmation.html.twig', [
                'fname' => $staff->getFname(),
                'token' => $staff->getConfirmationToken(),
                'expires' => $staff->getTokenExpiresAt(),
                'orgname' => $org->getOrgname(),
            ]);
            $mailParams = [
                'view' => $view,
                'recipient' => $staff->getEmail(),
                'subject' => 'Volunteer Connections',
            ];
            $mailer->appMailer($mailParams);

            // store entities
            $em->persist($staff);
            $em->persist($org);
            $em->flush();

            $this->addFlash(
                'success',
                'Nonprofit ' . $org->getOrgname() . ' is created but not yet activated'
            );
            $this->addFlash(
                'success',
                'Look for the confirmation email that has been sent to the address provided'
            );


            return $this->redirectToRoute('home');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Add a nonprofit',
                    'userHeader' => 'Staff Member',
                    'orgHeader' => 'Nonprofit',
                    'focusHeader' => "Nonprofit's Focus",
                    'templates' => $templates,
        ]);
    }

    /**
     * @Route("/confirm/{token}", name = "confirm")
     */
    public function confirm(TokenChecker $checker, EmailerService $mailer, $token = null)
    {
        $user = $checker->checkToken($token);
        if (null === $user) {
            return $this->redirectToRoute('home');
        }

        $class = get_class($user);

        $em = $this->getDoctrine()->getManager();
        // if token is expired, remove user
        $now = new \DateTime();
        if ($now > $user->getTokenExpiresAt()) {
            $this->addFlash(
                'danger',
                'Registration has expired. Please register again.'
            );
            switch ($class) {
                case Staff::class:
                    $path = 'register_org';
                    $org = $user->getNonprofit();
                    $em->remove($org);
                    break;

                case Volunteer::class:
                    $path = 'register_volunteer';
                    $em->remove($user);

                    // no break
                default:
                    break;
            }

            return $this->redirectToRoute($path);
        }

        $flashMessage = 'Account is confirmed';
        // send notice email
        switch ($class) {
            case Staff::class:
                $org = $user->getNonprofit();
                $org->setActive(true);
                $em->persist($org);
                // notice to admin
                $view = $this->renderView('Email/new_nonprofit_notice.html.twig', ['npo' => $org,]);
                $mailParams = [
                    'view' => $view,
                    'recipient' => $this->getParameter('app.npo_activator'),
                    'subject' => 'New Nonprofit Registration',
                ];

                $mailer->appMailer($mailParams);

                $flashMessage .= '; please wait for nonprofit activation to login';
                break;
            case Volunteer::class:
                $user->setReceiveEmail(true);
                break;
            default:
                break;
        }

        $user->setConfirmationToken(null);
        $user->setTokenExpiresAt(null);
        $user->setEnabled(true);
        $em->persist($user);

        $em->flush();

        $this->addFlash(
            'success',
            $flashMessage
        );

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/invite/{token}", name = "register_invite")
     */
    public function invitation(Request $request, TokenChecker $checker, UserPasswordEncoderInterface $passwordEncoder, EmailerService $mailer, $token = null)
    {
        $user = $checker->checkToken($token);
        if (null === $user) {
            return $this->redirectToRoute('home');
        }

        $class = get_class($user);
        if (Admin::class !== $class) {
            $this->addFlash(
                'danger',
                'Invalid registration data',
            );

            return $this->redirectToRoute('home');
        }

        $now = new \DateTime();
        if ($now > $user->getTokenExpiresAt()) {
            $this->addFlash(
                'danger',
                'Invitation has expired'
            );
            $view = $this->renderView('Email/expired_invite.html.twig', [
                'user' => $user,
            ]);
            $mailParams = [
                'view' => $view,
                'subject' => 'Expired invitation',
            ];
            $mailer->appMailer($mailParams);

            return $this->redirectToRoute('home');
        }
        // now set a password
        $templates = [
            'Default/_empty.html.twig',
            'Registration/_password.html.twig',
        ];
        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Your admin account is created!'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Set new password',
                    'templates' => $templates,
        ]);
    }

    private function staffProperties($data)
    {
        // create new staff entity
        $staff = new Staff();
        $staff->setFname($data['fname']);
        $staff->setSname($data['sname']);
        $staff->setEmail($data['email']);
        $staff->setEnabled(false);
        $password = $this->encoder->encodePassword($staff, $data['plainPassword']['first']);
        $staff->setPassword($password);
        $staff->setConfirmationToken(md5(uniqid(rand(), true)));
        $expiresAt = new \DateTime();
        $staff->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));

        return $staff;
    }

    private function volunteerProperties($volunteer, $data)
    {
        $password = $this->encoder->encodePassword($volunteer, $data['plainPassword']['first']);
        $volunteer->setPassword($password);
        $volunteer->setEnabled(false);
        $volunteer->setConfirmationToken(md5(uniqid(rand(), true)));
        $expiresAt = new \DateTime();
        $volunteer->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
    }
}

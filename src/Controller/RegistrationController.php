<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Staff;
use App\Entity\Volunteer;
use App\Entity\Nonprofit;
use App\Form\Type\NonprofitType;
use App\Form\Type\NewUserType;
use App\Form\Type\NewPasswordType;
use App\Form\Type\UserEmailType;
use App\Services\EmailerService;
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

        return $this->render('Email/forgot.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Request forgotten password form'
        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset_password")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token = null)
    {
        // for when either a logged in user or an unknown person: no token
        $em = $this->getDoctrine()->getManager();
        // make sure we're working with a logged in user
        if (null === $token) {
            $user = $this->getUser();
            if (null === $user) {
                $this->addFlash(
                        'danger',
                        'User not found'
                );

                return $this->redirectToRoute('home');
            }
        } else {
            // possible forgotten password user with token
            $person = $em->getRepository('App:User')->findOneBy(['confirmationToken' => $token]);
            if (null === $person) {
                $this->addFlash(
                        'danger',
                        'User not found'
                );

                return $this->redirectToRoute('home');
            }
            $user = $em->getRepository('App:User')->findOneBy(['email' => $person->getEmail()]);
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
        }
        $templates = [
            'Default/_empty.html.twig',
            'Registration/_password.html.twig',
        ];
        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
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

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
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
    public function confirm(Request $request, EmailerService $mailer, $token = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (null === $token) {
            $this->addFlash(
                    'danger',
                    'Confirmation status cannot be determined'
            );

            return $this->redirectToRoute('home');
        }
        // if bogus token data is presented
        $user = $em->getRepository('App:User')->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            $this->addFlash(
                    'danger',
                    'Invalid registration data'
            );

            return $this->redirectToRoute('home');
        }
        $actor = '';
        if ($user->hasRole('ROLE_STAFF')) {
            $actor = 'staff';
        } elseif ($user->hasRole('ROLE_VOLUNTEER')) {
            $actor = 'volunteer';
        }

        // if token is expired, remove user
        $now = new \DateTime();
        if ($now > $user->getTokenExpiresAt()) {
            $this->addFlash(
                    'danger',
                    'Confirmation has expired. Please register again.'
            );
            if ('staff' === $actor) {
                $path = 'register_org';
                $org = $user->getNonprofit();
                $em->remove($org);
            } elseif ('volunteer' === $actor) {
                $path = 'register_volunteer';
                $em->remove($user);
            }

            return $this->redirectToRoute($path);
        }

        $flashMessage = 'Account is confirmed';
        // send notice email
        if ('staff' === $actor) {
            $org = $user->getNonprofit();
            $org->setActive(true);
            $em->persist($org);
            // notice to admin
            $view = $this->renderView('Email/new_nonprofit_notice.html.twig', ['npo' => $org,]);
            $mailParams = [
                'view' => $view,
                'recipient' => $this->getParameter('app.npo_activater'),
                'subject' => 'New Nonprofit Registration',
            ];

            $mailer->appMailer($mailParams);

            $flashMessage .= '; please wait for nonprofit activation to login';
        } elseif ('volunteer' === $actor) {
            $user->setReceiveEmail(true);
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

    //  Note that User cannot be instantiated as it is now an abstract class!!!
    /**
     * @Route("/invite/{token}", name="complete_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token = null)
    {
        $em = $this->getDoctrine()->getManager();
        $invited = $em->getRepository('App:Invitation')->findOneBy(['confirmationToken' => $token]);

        // if bogus token data is presented
        if (null === $invited) {
            $this->addFlash(
                'danger',
                'Invalid registration data'
            );

            return $this->redirectToRoute('home');
        }

        $email = $invited->getEmail();
        $existingUser = $em->getRepository('App:User')->findOneBy(['email' => $email]);

        //if $invited has already registered
        if (null !== $existingUser) {
            $this->addFlash(
                'danger',
                'User has already registered'
            );

            return $this->redirectToRoute('home');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFname($invited->getFname());
        $user->setSname($invited->getSname());
        $user->setUsername($invited->getUsername());
        $form = $this->createForm(NewUserType::class, $user);


        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $user->setPassword(
                $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
            );
            $user->setEnabled(true);
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);

            // remove new user from invitation table
            $invitee = $em->getRepository('App:Invitation')->findOneBy(['email' => $user->getEmail()]);
            $em->remove($invitee);

            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash(
                'success',
                'You are now registered and may log in'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render(
            'Registration/register.html.twig',
            array('form' => $form->createView(),
                            'headerText' => 'Create new user',
                        )
        );
    }
}

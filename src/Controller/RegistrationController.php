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

    //  Note that User cannot be instantiated as it is now an abstract class!!!
//    /**
//     * @Route("/invite/{token}", name="complete_registration")
//     */
//    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token = null)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $invited = $em->getRepository('App:Invitation')->findOneBy(['confirmationToken' => $token]);
//
//        // if bogus token data is presented
//        if (null === $invited) {
//            $this->addFlash(
//                'danger',
//                'Invalid registration data'
//            );
//
//            return $this->redirectToRoute('home');
//        }
//
//        $email = $invited->getEmail();
//        $existingUser = $em->getRepository('App:User')->findOneBy(['email' => $email]);
//
//        //if $invited has already registered
//        if (null !== $existingUser) {
//            $this->addFlash(
//                'danger',
//                'User has already registered'
//            );
//
//            return $this->redirectToRoute('home');
//        }
//
//        $user = new User();
//        $user->setEmail($email);
//        $user->setFname($invited->getFname());
//        $user->setSname($invited->getSname());
//        $user->setUsername($invited->getUsername());
//        $form = $this->createForm(NewUserType::class, $user);
//
//
//        // 2) handle the submit (will only happen on POST)
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            // 3) Encode the password (you could also do this via Doctrine listener)
//            $user->setPassword(
//                $passwordEncoder->encodePassword(
//                        $user,
//                        $form->get('plainPassword')->getData()
//                    )
//            );
//            $user->setEnabled(true);
//            $user->setRoles(['ROLE_USER']);
//            $em->persist($user);
//
//            // remove new user from invitation table
//            $invitee = $em->getRepository('App:Invitation')->findOneBy(['email' => $user->getEmail()]);
//            $em->remove($invitee);
//
//            $em->flush();
//
//            // ... do any other work - like sending them an email, etc
//            // maybe set a "flash" success message for the user
//            $this->addFlash(
//                'success',
//                'You are now registered and may log in'
//            );
//
//            return $this->redirectToRoute('home');
//        }
//
//        return $this->render(
//            'Registration/register.html.twig',
//            array('form' => $form->createView(),
//                            'headerText' => 'Create new user',
//                        )
//        );
//    }

    /**
     * Render a form to submit email address
     *
     * @Route("/forgot", name="register_forgot")
     */
    public function forgotPassword(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(UserEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('user_email')['email'];
            $em = $this->getDoctrine()->getManager();
            $sender = $this->getParameter('swiftmailer.sender_address');
            $user = $em->getRepository('App:User')->findOneBy(['email' => $email]);
            $this->addFlash(
                    'success',
                    'Email sent to address provided'
            );

            // if nonUser
            if (null === $user) {
                $nonUserView = $this->renderView('Email/nonUser.html.twig');
                $message = (new \Swift_Message('Project MANA forgotten password'))
                        ->setFrom($sender)
                        ->setTo($email)
                        ->setBody($nonUserView, 'text/html')
                ;
                $mailer->send($message);

                return $this->redirectToRoute('home');
            }

            $token = md5(uniqid(rand(), true));
            $expiresAt = new \DateTime();
            $user->setPasswordExpiresAt($expiresAt->add(new \DateInterval('PT3H')));

            $forgotView = $this->renderView(
                    'Email/forgotten.html.twig',
                    [
                        'fname' => $user->getFname(),
                        'token' => $token,
                        'expiresAt' => $expiresAt,
                    ]
                    )
            ;

            $message = (new \Swift_Message('Project MANA forgotten password'))
                    ->setFrom($sender)
                    ->setTo($email)
                    ->setBody($forgotView, 'text/html')
            ;
            $mailer->send($message);

            $user->setConfirmationToken($token);
            $user->setPasswordExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
            $em->persist($user);
            $em->flush();

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
            $expiresAt = $user->getPasswordExpiresAt();
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
        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $user->setPassword(
                    $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                    )
            );
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

        return $this->render('Registration/register.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Set new password',
        ]);
    }

    /**
     * @Route("/volunteer", name="register_volunteer")
     */
    public function registerVolunteer(Request $request, \Swift_Mailer $mailer)
    {
        $volunteer = new Volunteer();
        $form = $this->createForm(NewUserType::class, $volunteer, ['data_class' => Volunteer::class]);
        $templates = [
            'Registration/new_user.html.twig',
            'Registration/focuses.html.twig',
            'Registration/skills.html.twig',
        ];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $request->request->get('new_user');
            $this->volunteerProperties($volunteer, $userData);

            // send confirmation email
            $sender = $this->getParameter('swiftmailer.sender_address');
            $view = $this->renderView(
                    'Email/volunteerConfirmation.html.twig',
                    [
                        'fname' => $volunteer->getFname(),
                        'token' => $volunteer->getConfirmationToken(),
                        'expires' => $volunteer->getTokenExpiresAt(),
                    ]
            );
            $message = (new \Swift_Message('Volunteer Connections'))
                    ->setFrom($sender)
                    ->setTo($volunteer->getEmail())
                    ->setBody(
                    $view,
                    'text/html'
                    )
            ;
            $mailer->send($message);

            $em = $this->getDoctrine()->getManager();
            $em->persist($volunteer);
            $em->flush();
            $this->addFlash(
                    'success',
                    'A volunteer registration confirmation has been sent to your email address'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Default/formTemplates.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Become a volunteer',
                    'userHeader' => 'Volunteer',
                    'focusHeader' => "Volunteer's Focus(es)",
                    'skillHeader' => "Volunteer's Skill(s)",
                    'templates' => $templates,
        ]);
    }

    /**
     * @Route("/nonprofit", name="register_org")
     */
    public function registerNonprofit(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $org = new Nonprofit();
        $form = $this->createForm(NonprofitType::class, $org);
        $templates = [
            'Registration/nonprofit.html.twig',
            'Registration/new_user.html.twig',
            'Registration/focuses.html.twig',
        ];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orgData = $request->request->get('org');
            $em = $this->getDoctrine()->getManager();
            $staff = $this->staffProperties($orgData['staff']);
            $org->setStaff($staff);

            // send confirmation email
            $sender = $this->getParameter('swiftmailer.sender_address');
            $view = $this->renderView(
                    'Email/staffConfirmation.html.twig',
                    [
                        'fname' => $staff->getFname(),
                        'token' => $staff->getConfirmationToken(),
                        'expires' => $staff->getTokenExpiresAt(),
                        'orgname' => $org->getOrgname(),
                    ]
            );
            $message = (new \Swift_Message('Volunteer Connections'))
                    ->setFrom($sender)
                    ->setTo($staff->getEmail())
                    ->setBody(
                    $view,
                    'text/html'
                    )
            ;
            $mailer->send($message);

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

        return $this->render('Default/formTemplates.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Add a nonprofit',
                    'userHeader' => 'Staff Member',
                    'orgHeader' => 'Nonprofit',
                    'focusHeader' => "Nonprofit's Focus",
                    'templates' => $templates,
        ]);
    }

    /**
     * @Route("/confirm/{token}")
     */
    public function confirm(UserPasswordEncoderInterface $encoder, $token = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(['confirmationToken' => $token]);

        // if bogus token data is presented
        if (null === $user) {
            $this->addFlash(
                    'danger',
                    'Invalid registration data'
            );

            return $this->redirectToRoute('home');
        }
        
        // expired token?
        if (\DateTime() > $user->getTokenExpiresAt()) {
            $this->addFlash(
                    'danger',
                    'Confirmation has expired'
            );

            return $this->redirectToRoute('home');
        }
        
        $this->addFlash(
                'danger',
                'Filler text'
        );
        return $this->redirectToRoute('home');
    }

    private function staffProperties($data)
    {
        // create new staff entity
        $staff = new Staff();
        $staff->setFname($data['fname']);
        $staff->setSname($data['sname']);
        $staff->setEmail($data['email']);
        $staff->setEnabled(true);
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
        $volunteer->setEnabled(true);
        $volunteer->setConfirmationToken(md5(uniqid(rand(), true)));
        $expiresAt = new \DateTime();
        $volunteer->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
    }

}

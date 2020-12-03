<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Nonprofit;
use App\Entity\Person;
use App\Form\Type\NonprofitType;
use App\Form\Type\UserType;
use App\Form\Type\NewPasswordType;
use App\Form\Type\Field\UserEmailType;
use App\Services\EmailerService;
use App\Services\RegistrationService;
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
    private $mailer;
    private $regSvc;

    public function __construct(
            UserPasswordEncoderInterface $encoder,
            EmailerService $mailer,
            RegistrationService $regSvc
    )
    {
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->regSvc = $regSvc;
    }

    /**
     * Render a form to submit email address
     *
     * @Route("/forgot", name="register_forgot")
     */
    public function forgotPassword(Request $request)
    {
        $form = $this->createForm(UserEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('user_email')['email'];
            $em = $this->getDoctrine()->getManager();
            $sender = $this->getParameter('app.sender_address');
            $user = $em->getRepository(Person::class)->findOneBy(['email' => $email]);
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
            $this->mailer->appMailer($mailParams);

            return $this->redirectToRoute('home_page');
        }

        return $this->render('Registration/forgot.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Request forgotten password form'
        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset_password")
     */
    public function resetPassword(Request $request, TokenChecker $checker, $token = null)
    {
        $user = $this->getUser() ?? $checker->checkToken($token) ?? null;
        if (null === $user) {
            return $this->redirectToRoute('home_page');
        }
        $expiresAt = $user->getTokenExpiresAt();
        $now = new \DateTime();

        // has token expired?
        if (!is_null($expiresAt) && $now > $expiresAt) {
            $this->addFlash(
                    'danger',
                    'Password link has expired'
            );

            return $this->redirectToRoute('home_page');
        }

        $header = ['center' => ''];
        $entity_form['center'] = [
            'Entity/_name_vars.html.twig',
            'Entity/_email_var.html.twig',
            'Entity/_user_plain_password.html.twig',
                ]
        ;

        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setPassword(
                    $this->encoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                    )
            );
            // if user is a staff replacement accept user
            if ('Replacement' === $user->getReplacementStatus()) {
                $nonprofit = $user->getNonprofit();
                $replacedStaff = $em->getRepository(Person::class)->findOneBy(['replacementStatus' => 'Pending', 'nonprofit' => $nonprofit]);
                $replacedStaff->setReplacementStatus("Replaced");
                $replacedStaff->setEnabled(false);
                $user->setReplacementStatus('Replace');
                $user->setCompleted(new \DateTime());
                $user->setConfirmationToken(null);
                $user->setEnabled(true);
                $user->setCompleted(new \DateTime());

                $this->addFlash(
                        'success',
                        'You are now the registered representative for ' . $nonprofit->getOrgname(),
                );
            } else {
                $this->addFlash(
                        'success',
                        'Your password has been updated'
                );
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home_page');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Set new password',
                    'header' => $header,
                    'entity_form' => $entity_form,
        ]);
    }

    /**
     * @Route("/person/{type}", name="register_person")
     */
    public function registerPerson(Request $request, $type)
    {
        $person = null;
        if ('volunteer' === $type && null === $this->getUser()) {
            $person = new Person();
            $person->addRole('ROLE_VOLUNTEER');
            $preReg = $this->regSvc->preRegVolunteer();
            $header = $preReg['header'];
            $headerText = 'Become a volunteer';
            $entity_form = $preReg['entityForm'];
            $form = $this->createForm(UserType::class, $person, [
                'register' => true,
            ]);
        }

        if ('admin' === $type && $this->isGranted('ROLE_ADMIN')) {
            $person = new Person();
            $person->addRole('ROLE_ADMIN');
            $preReg = $this->regSvc->preRegAdmin();
            $header = $preReg['header'];
            $headerText = 'Invite an admin';
            $entity_form = $preReg['entityForm'];
            $form = $this->createForm(UserType::class, $person, [
                'password' => false,
            ]);
        }

        if (null === $person) {
            $this->addFlash('warning', 'Registration is not available');

            return $this->redirectToRoute('home_page');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $request->request->get('user');
            $propMethod = $type . 'Properties';
            $this->$propMethod($person, $userData);
            // send confirmation email
            $postRegMethod = $type . 'PostReg';
            $mailParams = $this->regSvc->$postRegMethod($person);
            $this->mailer->appMailer($mailParams);

            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            if (isset($preReg['flashMessage'])) {
                $this->addFlash('success', $preReg['flashMessage']);
            }

            return $this->redirectToRoute('home_page');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => $headerText,
                    'header' => $header,
                    'entity_form' => $entity_form,
        ]);
    }

    /**
     * @Route("/nonprofit", name="register_org")
     */
    public function registerNonprofit(Request $request)
    {
        $org = new Nonprofit();
        $form = $this->createForm(NonprofitType::class, $org, [
            'register' => true,
        ]);
        $preReg = $this->regSvc->preRegNonprofit();
        $header = $preReg['header'];
        $entity_form = $preReg['entityForm'];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orgData = $request->request->get('org');
            $em = $this->getDoctrine()->getManager();
            $rep = $this->repProperties($orgData['rep']);
            $postRegMethod = 'nonprofitPostReg';
            $mailParams = $this->regSvc->$postRegMethod($org, $rep);
            $this->mailer->appMailer($mailParams);

            // store entities
            $em->persist($rep);
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

            return $this->redirectToRoute('home_page');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Add a nonprofit',
                    'header' => $header,
                    'entity_form' => $entity_form
        ]);
    }

    /**
     * @Route("/confirm/{token}", name = "confirm")
     */
    public function confirm(TokenChecker $checker, $token = null)
    {
        $user = $checker->checkToken($token);
        if (null === $user) {
            return $this->redirectToRoute('home_page');
        }
        $roles = $user->getRoles();
        $class = '';
        $messageType = 'Registration';
        if (in_array('ROLE_VOLUNTEER', $roles)) {
            $class = 'volunteer';
        } elseif (in_array('ROLE_REP', $roles)) {
            $class = 'rep';
        } elseif (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)) {
            $class = 'admin';
            $messageType = 'Invitation';
        }
        $em = $this->getDoctrine()->getManager();
        // if token is expired, remove user
        $now = new \DateTime();
        if ($now > $user->getTokenExpiresAt()) {
            $this->addFlash(
                    'danger',
                    $messageType . ' has expired. Please register again.'
            );
            switch ($class) {
                case 'rep':
                    $path = 'register_org';
                    $org = $user->getNonprofit();
                    $em->remove($org);
                    break;
                case 'admin':
                case 'volunteer':
                    $path = 'register_person';
                    $em->remove($user);
                default:
                    break;
            }

            return $this->redirectToRoute($path);
        }

        $flashMessage = 'Account is confirmed';
        // send notice email
        switch ($class) {
            case 'rep':
                $org = $user->getNonprofit();
                $org->setActive(true);
                $em->persist($org);
                // notice to admin
                $view = $this->renderView('Email/new_nonprofit_notice.html.twig', ['npo' => $org,]);
                $mailParams = [
                    'view' => $view,
                    'recipient' => $this->mailer->getSender(),
                    'subject' => 'New Nonprofit Registration',
                ];

                $this->mailer->appMailer($mailParams);

                $flashMessage .= '; please wait for nonprofit activation to login';
                break;
            case 'volunteer':
                $user->setReceiveEmail(true);
                break;
            case 'admin':
                $flashMessage .= '; thank you for accepting. You may now log in';
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
     * @Route("/replaceStaff/{id}", name="replace_staff")
     */
    public function replaceStaff(Request $request, $id)
    {
        if (null === $id || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home_page');
        }

        $replacement = new Person();
        $replacement->addRole('ROLE_REP');
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(Person::class)->find($id);
        $nonprofit = $rep->getNonprofit();

        $header = ['center' => ''];
        $entity_form['center'] = [
            'Entity/_user_name.html.twig',
            'Entity/_user_email.html.twig',
        ];
        $form = $this->createForm(UserType::class, $replacement, [
            'password' => false,
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
            $this->mailer->appMailer($mailParams);

            $em->persist($rep);
            $em->persist($replacement);
            $em->flush();

            $this->addFlash('success', 'Replacement email sent');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'staff' => $rep,
                    'header' => $header,
                    'entity_form' => $entity_form,
                    'headerText' => 'Replacement for ' . $rep->getFullName() . '<br />' .
                    $rep->getNonprofit()->getOrgname(),
                ])
        ;
    }

    private function repProperties($data)
    {
        // create new staff entity
        $person = new Person();
        $person->addRole('ROLE_REP');
        $person->setFname($data['fname']);
        $person->setSname($data['sname']);
        $person->setEmail($data['email']);
        $password = $this->encoder->encodePassword($person, $data['plainPassword']['first']);
        $this->commonProperties($person, $password);
        $person->setReplacementStatus('Replace');

        return $person;
    }

    private function volunteerProperties($person, $data)
    {
        $password = $this->encoder->encodePassword($person, $data['plainPassword']['first']);
        $this->commonProperties($person, $password);
    }

    private function adminProperties($person, $data)
    {
        $password = 'new_admin';
        $this->commonProperties($person, $password);
        $person->setMailer(false);
    }

    private function commonProperties($person, $password)
    {
        $password = $this->encoder->encodePassword($person, $password);
        $person->setPassword($password);
        $person->setEnabled(false);
        $person->setConfirmationToken(md5(uniqid(rand(), true)));
        $expiresAt = new \DateTime();
        $person->setTokenExpiresAt($expiresAt->add(new \DateInterval('PT3H')));
    }

}

<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/ProfileController.php

namespace App\Controller;

use App\Entity\Opportunity;
use App\Entity\Volunteer;
use App\Form\Type\NonprofitType;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    /**
     * @Route("/nonprofit", name="profile_nonprofit")
     */
    public function nonprofit(Request $request)
    {
        $user = $this->getUser();
        if (!$user || !$user->hasRole('ROLE_STAFF')) {
            return $this->redirectToRoute('home');
        }
        $em = $this->getDoctrine()->getManager();
        $npo = $user->getNonprofit();
        $opps = $em->getRepository(Opportunity::class)->findBy(['nonprofit' => $npo], ['oppname' => 'ASC']);
        $templates = [
            'Nonprofit/nonprofit_form.html.twig',
            'Default/focuses.html.twig',
            'Nonprofit/opportunities.html.twig',
        ];
        $headerText = $npo->getOrgname() . ' profile';
        $form = $this->createForm(NonprofitType::class, $npo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($npo);
            $em->flush();
            $this->addFlash(
                    'success',
                    'Profile updated'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => $templates,
                    'headerText' => $headerText,
                    'npo' => $npo,
                    'focusHeader' => $npo->getOrgname() . "'s Focus(es)",
                    'opportunities' => $opps,
        ]);
    }

    /**
     * @Route("/person", name="profile_person")
     */
    public function volunteer(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('home');
        }
        $em = $this->getDoctrine()->getManager();
        $templates = [
            'Profile/user.html.twig',
        ];
        if (Volunteer::class === get_class($user)) {
            $templates[] = 'Default/focuses.html.twig';
            $templates[] = 'Default/skills.html.twig';
        }
        $headerText = $user->getFname() . ' ' . $user->getSname() . ' profile';
        $form = $this->createForm(UserType::class, $user, [
            'data_class' => get_class($user),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash(
                    'success',
                    'Profile updated'
            );

            return $this->redirectToRoute('home');
        }
        $options = [
            'form' => $form->createView(),
            'templates' => $templates,
            'headerText' => $headerText,
        ];
        if (Volunteer::class === get_class($user)) {
            $options['focusHeader'] = "Volunteer's Focus(es)";
            $options['skillHeader'] = "Volunteer's Skill(s)";
        }
         
        return $this->render('Default/form_templates.html.twig', $options);
    }

//    /**
//     * @Route("/", name="profile")
//     */
//    public function index(Request $request)
//    {
//        $user = $this->getUser();
//        if (!$user) {
//            return $this->redirectToRoute('home');
//        }
//
//        $em = $this->getDoctrine()->getManager();
//        if (Volunteer::class === get_class($user)) {
//            $entity = $user;
//            $templates = [
//                'Profile/user.html.twig',
////                'Volunteer/receive_email_form.html.twig',
//                'Default/focuses.html.twig',
//                'Default/skills.html.twig',
//            ];
//            $headerText = $user->getFname() . ' ' . $user->getSname() . ' profile';
//            $form = $this->createForm(UserType::class, $user, [
//                'data_class' => Volunteer::class,
//            ]);
//            $destination = $this->render('Default/form_templates.html.twig', [
//                'form' => $form->createView(),
//                'templates' => $templates,
//                'headerText' => $headerText,
//                'focusHeader' => "Volunteer's Focus(es)",
//                'skillHeader' => "Volunteer's Skill(s)",
//            ]);
//        }
//
//        if (Staff::class === get_class($user)) {
//            $entity = $npo = $user->getNonprofit();
//            $opps = $em->getRepository(Opportunity::class)->findBy(['nonprofit' => $npo], ['oppname' => 'ASC']);
//            $templates = [
//                'Nonprofit/nonprofit_form.html.twig',
//                'Default/focuses.html.twig',
//                'Nonprofit/opportunities.html.twig',
//            ];
//            $headerText = $npo->getOrgname() . ' profile';
//            $form = $this->createForm(NonprofitType::class, $npo);
//            $destination = $this->render('Default/form_templates.html.twig', [
//                'form' => $form->createView(),
//                'templates' => $templates,
//                'headerText' => $headerText,
//                'npo' => $npo,
//                'focusHeader' => $npo->getOrgname() . "'s Focus(es)",
//                'opportunities' => $opps,
//            ]);
//        }
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em->persist($entity);
//            $em->flush();
//            $this->addFlash(
//                    'success',
//                    'Profile updated'
//            );
//
//            return $this->redirectToRoute('home');
//        }
//
//        return $destination;
//    }

}

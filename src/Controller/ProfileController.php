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
use App\Entity\Representative;
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
        if (!$user || !$user->hasRole('ROLE_REP')) {
            return $this->redirectToRoute('home');
        }
        $em = $this->getDoctrine()->getManager();
        $npo = $user->getNonprofit();
        $opps = $em->getRepository(Opportunity::class)->findBy(['nonprofit' => $npo], ['oppname' => 'ASC']);
        $templates = [
            'Nonprofit/_nonprofit_form.html.twig',
            'Default/_focuses.html.twig',
            'Nonprofit/_opportunities.html.twig',
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
                    'rep' => $user,
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
        if (Representative::class === get_class($user)) {
            $templates[] = 'Default/_empty.html.twig';
        }
        $templates[] = 'Profile/_user.html.twig';
        if (Volunteer::class === get_class($user)) {
            $templates[] = 'Default/_focuses.html.twig';
            $templates[] = 'Default/_skills.html.twig';
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
}

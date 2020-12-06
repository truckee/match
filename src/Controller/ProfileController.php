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
use App\Entity\Person;
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
            return $this->redirectToRoute('home_page');
        }
        $em = $this->getDoctrine()->getManager();
        $npo = $user->getNonprofit();
        $opps = $em->getRepository(Opportunity::class)->findBy(['nonprofit' => $npo], ['oppname' => 'ASC']);

        $header = ['left' => 'Nonprofit Organization',
            'center' => "Nonprofit's Focus(es)",
            'right' => $npo->getOrgname() . " Opportunities",
        ];
        $entity_form['left'] = ['Nonprofit/_nonprofit_form.html.twig'];
        $entity_form['center'] = ['Default/_focuses.html.twig'];
        $entity_form['right'] = ['Nonprofit/_opportunities.html.twig'];
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

            return $this->redirectToRoute('home_page');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => $headerText,
                    'npo' => $npo,
                    'rep' => $user,
                    'header' => $header,
                    'entity_form' => $entity_form,
                    'opportunities' => $opps,
        ]);
    }

    /**
     * @Route("/person", name="profile_person")
     */
    public function person(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('home_page');
        }
        $em = $this->getDoctrine()->getManager();

        if ($user->hasRole('ROLE_REP') || $user->hasRole('ROLE_ADMIN')) {
            $header = ['center' => $user->getFullname()];
            $entity_form['center'] = [
                'Entity/_user_name.html.twig',
            ];
        }
        if ($user->hasRole('ROLE_VOLUNTEER')) {
            $header = [
                'left' => $user->getFullname(),
                'center' => "Volunteer's Focus(es)",
                'right' => "Volunteer's Skill(s)",
            ];
            $entity_form['left'] = [
                'Entity/_user_name.html.twig',
                'Entity/_vol_receive_email.html.twig',
            ];
            $entity_form['center'] = ['Default/_focuses.html.twig'];
            $entity_form['right'] = ['Default/_skills.html.twig'];
        }

        $headerText = $user->getFname() . ' ' . $user->getSname() . ' profile';
        $form = $this->createForm(UserType::class, $user, [
            'register' => false,
            'password' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash(
                    'success',
                    'Profile updated'
            );

            return $this->redirectToRoute('home_page');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => $headerText,
                    'header' => $header,
                    'entity_form' => $entity_form,
                        ]
        );
    }

}

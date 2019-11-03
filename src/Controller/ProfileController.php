<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/ProfileController.php

namespace App\Controller;

use App\Entity\Volunteer;
use App\Form\Type\VolunteerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    /**
     * @Route("/", name="profile")
     */
    public function index(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//        $this->denyAccessUnlessGranted('ROLE_VOLUNTEER');
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('home');
        }
        if (Volunteer::class === get_class($user)) {
            $templates = [
                'Volunteer/receive_email_form.html.twig',
                'Default/focuses.html.twig',
                'Default/skills.html.twig',
            ];
            $headerText = $user->getFname() . ' ' . $user->getSname() . ' profile';
            $form = $this->createForm(VolunteerType::class, $user,);
            $destination = $this->render('Default/form_templates.html.twig', [
                'form' => $form->createView(),
                'templates' => $templates,
                'headerText' => $headerText,
                'focusHeader' => "Volunteer's Focus(es)",
                'skillHeader' => "Volunteer's Skill(s)",
            ]);
//            return $this->render('Volunteer/profile_form.html.twig', [
//                'form'=>$form->createView(),
//            ]);
        }
//        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
        }
//        dd(Volunteer::class);

        return $destination;
    }

}

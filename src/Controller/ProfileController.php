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
use App\Form\Type\ProfileType;
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
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        }
//        dd(Volunteer::class);
        if (Volunteer::class === get_class($user)) {
//            $destination = $this->render('Volunteer/profile_form.html.twig', [
//                'form'=>$form->createView(),
//            ]);
            return $this->render('Volunteer/profile_form.html.twig', [
                'form'=>$form->createView(),
            ]);
        }
        
//        return $destination;
    }
}

<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/ProfileController.php

namespace App\Controller;

//use App\Entity\User;
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
     * @Route("/volunteer/", name="volunteer_profile")
     */
    public function volunteerProfile(Request $request)
    {
//        $this->denyAccessUnlessGranted('ROLE_VOLUNTEER');
        $user = $this->getUser();
        if (null === $user || (!$user->hasRole('role_user') && $user->hasRole('role_Admin'))) {
            dd('not allowed');
        }
        $form = $this->createForm(UserType::class, $user, ['is_volunteer' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        }
        
        return $this->render('User/testRegister.html.twig', [
                    'form' => $form->createView()
        ]);
    }
}

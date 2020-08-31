<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/NonprofitController.php

namespace App\Controller;

use App\Entity\Nonprofit;
use App\Entity\Representative;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/nonprofit")
 */
class NonprofitController extends AbstractController
{

    /**
     * @Route("/", name="nonprofit_index")
     */
    public function index()
    {
        return $this->render('Nonprofit/index.html.twig');
    }

    /**
     * @Route("/view/{id}", name = "npo_view")
     */
    public function view($id)
    {
        $em = $this->getDoctrine()->getManager();
        $npo = $em->getRepository(Nonprofit::class)->find($id);
        if (null === $npo) {
            $this->addFlash('warning', 'Nonprofit not found');

            return $this->redirectToRoute('home');
        }
        $rep = $em->getRepository(Representative::class)->findOneBy(['nonprofit' => $npo, 'replacementStatus' => 'Replace']);
        
        return $this->render('Nonprofit/nonprofit_view.html.twig', [
            'npo'=>$npo,
            'staff' => $rep,
            ]);
    }
}

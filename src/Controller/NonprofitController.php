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
use App\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/view/{npo}", name = "npo_view")
     * @ParamConverter("npo", class="App:Nonprofit")
     */
    public function view($npo)
    {
        if (!is_object($npo)) {
            $this->addFlash('warning', 'Nonprofit object not found');

            return $this->redirectToRoute('home_page');
        }
        $rep = $em->getRepository(Person::class)->findOneBy(['nonprofit' => $npo, 'replacementStatus' => 'Replace']);

        return $this->render('Nonprofit/nonprofit_view.html.twig', [
                    'npo' => $npo,
                    'rep' => $rep,
        ]);
    }

}

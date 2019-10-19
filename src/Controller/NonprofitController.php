<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/NonprofitController.php

namespace App\Controller;

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
}

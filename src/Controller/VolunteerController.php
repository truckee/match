<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/VolunteerController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/volunteer")
 */
class VolunteerController extends AbstractController
{
    /**
     * @Route("/", name="vol_index")
     */
    public function index()
    {
        return $this->render('Volunteer/index.html.twig');
    }
}

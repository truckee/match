<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/Admin.php

namespace App\Controller;

use App\Controller\Admin\NonprofitCrudController;
use App\Controller\Admin\RepresentativeCrudController;
use App\Controller\Admin\VolunteerCrudController;
use App\Entity\Nonprofit;
use App\Entity\Person;
use App\Services\AdminServices;
use App\Services\ChartService;
use App\Services\PersonService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    private $crudUrlGenerator;
    private $adminSvc;
    private $userSvc;

    public function __construct(CrudUrlGenerator $crudUrlGenerator, PersonService $userSvc, AdminServices $adminSvc)
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->adminSvc = $adminSvc;
        $this->userSvc = $userSvc;
    }

    /**
     * @Route("/admin/dashboard", name="dashboard")
     *
     */
    public function index(ChartService $charter)
    {
        $volChart = $charter->volunteerChart();
        $searchGauge = $charter->searchGauge();
        $focus = $charter->sankeyFocus();

        return $this->render('Admin/index.html.twig', [
                    'vol_chart' => $volChart,
                    'search_gauge' => $searchGauge,
                    'focus' => $focus
        ]);
    }

    /**
     * Activates or deactivates a nonprofit
     *
     * @Route("/admin/status/{id}", name="status")
     */
    public function statusChange($id = null)
    {
        $url = $this->crudUrlGenerator
                ->build()
                ->setController(NonprofitCrudController::class)
                ->setAction(Action::INDEX);

        $em = $this->getDoctrine()->getManager();
        $npo = $em->getRepository(Nonprofit::class)->find($id);
        if (null === $npo) {
            $this->addFlash(
                    'warning',
                    'Nonprofit not found'
            );

            return $this->redirectToRoute('dashboard');
        }

        $statusMessage = $this->adminSvc->statusChange($npo);
        $this->addFlash(
                'success',
                $statusMessage
        );

        return $this->redirect($url);
    }

    /**
     * @Route("/admin/lock/{id}", name = "lock_user")
     */
    public function lockUser($id)
    {
        if (null === $id) {
            return;
        }
        $class = $this->userSvc->roleConverter($id);
        $flashMessage = $this->userSvc->lockUser($id);
        $this->addFlash($flashMessage['type'], $flashMessage['content']);
        $url = $this->returnToAdmin($class);

        return $this->redirect($url);
    }

    /**
     * @Route("/admin/switch/{src}", name="admin_switch")
     */
    public function switch($src)
    {
        //'src' = class~'-'~name~'-'~id
        $param = explode('-', $src);
        $class = $param[0];
        $field = $param[1];
        $id = $param[2];
        if ('Person' === $class) {
            $class = $this->userSvc->roleConverter($id);
        }

        $flashMessage = $this->userSvc->switchFns($class, $id, $field);
        $this->addFlash($flashMessage['type'], $flashMessage['content']);
        $url = $this->returnToAdmin($class);

        return $this->redirect($url);
    }

    public function returnToAdmin($class)
    {
        $controller = 'App\\Controller\\Admin\\' . $class . 'CrudController';
        $url = $this->crudUrlGenerator
                ->build()
                ->setController($controller)
                ->setAction(Action::INDEX);

        return $url;
    }

}

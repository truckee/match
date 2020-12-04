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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    private $crudUrlGenerator;
    private $userSvc;

    public function __construct(CrudUrlGenerator $crudUrlGenerator, PersonService $userSvc)
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->userSvc = $userSvc;
    }

    /**
     * @Route("/dashboard", name="dashboard")
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
     * @Route("/status/{id}", name="status")
     */
    public function statusChange(AdminServices $adminSvc, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $npo = $em->getRepository(Nonprofit::class)->find($id);
        if (null === $npo) {
            $this->addFlash(
                    'warning',
                    'Nonprofit not found'
            );

            return $this->redirectToRoute('dashboard');
        }

        $statusMessage = $adminSvc->statusChange($npo);
        $this->addFlash(
                'success',
                $statusMessage
        );
        $url = $this->crudUrlGenerator
                ->build()
                ->setController(NonprofitCrudController::class)
                ->setAction(Action::INDEX);

        return $this->redirect($url);
    }

    /**
     * @Route("/lock/{id}", name = "lock_user")
     */
    public function lockUser($id)
    {
        if (null === $id) {
            return;
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Person::class)->find($id);
        $state = $user->getLocked();
        $user->setLocked(!$state);
        $em->persist($user);

        if ($user->hasRole('ROLE_REP')) {
            $controller = RepresentativeCrudController::class;
            $nonprofit = $user->getNonprofit();
            $nonprofit->setActive(false);
            $em->persist($nonprofit);
        } elseif ($user->hasRole('ROLE_VOLUNTEER')) {
            $controller = VolunteerCrudController::class;
        }
        $em->flush();
        $lockState = $user->getLocked() ? ' is now locked' : ' is now unlocked';
        $this->addFlash('success', $user->getFullName() . $lockState);
        $url = $this->crudUrlGenerator
                ->build()
                ->setController($controller)
                ->setAction(Action::INDEX);

        return $this->redirect($url);
    }

//
//    /**
//     * @Route("/assign/{id}", name="assign_mailer")
//     */
//    public function assign($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository(Admin::class)->findAll();
//        foreach ($entities as $admin) {
//            if ((int) $id === $admin->getId()) {
//                $admin->setMailer(true);
//                $em->persist($admin);
//            } else {
//                $admin->setMailer(false);
//                $em->persist($admin);
//            }
//        }
//        $em->flush();
//
//        $response = new JsonResponse(json_encode($id));
//        return $response;
//    }
//
//    /**
//     * @Route("/enabler", name = "admin_enabler")
//     */
//    public function enabler(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $id = $request->query->get('id');
//        $admin = $em->getRepository(Admin::class)->find($id);
//        $enabled = $admin->isEnabled();
//        if (!$admin->getSender() && !$admin->hasRole('ROLE_SUPER_ADMIN')) {
//            $admin->setEnabled(!$enabled);
//            $em->persist($admin);
//            $em->flush();
//        } else {
//            $this->addFlash('danger', $admin->getFullName() . ' cannot be disabled');
//        }
//
//        return $this->redirectToRoute('easyadmin', array(
//            'action' => 'list',
//            'entity' => $request->query->get('entity'),
//        ));    }

    /**
     * @Route("/switch/{id}/{field}", name="admin_switch")
     */
    public function switch($id, $field)
    {
        $class = $this->userSvc->roleConverter($id);

        switch ($class):
            case 'Admin':
                if ('mailer' === $field) {
                    $this->mailer($id);
                }
                if ('enabled' === $field) {
                    $this->adminEnabler($id);
                }
                break;
            default:
                $this->enabler($field, $id);
                break;
        endswitch;

        $controller = 'App\\Controller\\Admin\\' . $class . 'CrudController';
        $url = $this->crudUrlGenerator
                ->build()
                ->setController($controller)
                ->setAction(Action::INDEX);

        return $this->redirect($url);
    }

    private function mailer($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mailer = $em->getRepository(Person::class)->findOneBy(['mailer' => true]);

        if ((int) $id === $mailer->getId()) {
            return;
        }

        $selected = $em->getRepository(Person::class)->find($id);
        if (false === $selected->getEnabled()) {
            $this->addFlash('warning', 'Disabled admins cannot be mailer');

            return;
        }

        $entities = $em->getRepository(Person::class)->findBy(['enabled' => true]);
        foreach ($entities as $admin) {
            if ((int) $id === $admin->getId()) {
                $admin->setMailer(true);
            } else {
                $admin->setMailer(false);
            }
            $em->persist($admin);
        }
        $em->flush();
    }

    private function enabler($field, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $vol = $em->getRepository(Person::class)->find($id);
        $getter = 'get' . ucfirst($field);
        $setter = 'set' . ucfirst($field);
        $value = $vol->$getter();
        $vol->$setter(!$value);
        $em->persist($vol);
        $em->flush();
    }

    private function adminEnabler($id)
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $em->getRepository(Person::class)->find($id);
        $enabled = $admin->getEnabled();
        if (!$admin->getMailer() && !$admin->hasRole('ROLE_SUPER_ADMIN')) {
            $admin->setEnabled(!$enabled);
            $em->persist($admin);
            $em->flush();
        } else {
            $this->addFlash('danger', $admin->getFullName() . ' cannot be disabled');
        }
    }

}

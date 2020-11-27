<?php

namespace App\Controller\Admin;

use App\Entity\Focus;
use App\Entity\Nonprofit;
use App\Entity\Person;
use App\Entity\Skill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class DashboardController extends AbstractDashboardController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
        return $this->redirect($routeBuilder->setController(NonprofitCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
                        ->setTitle('ConnectionsReno');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Nonprofit', 'fas fa-folder-open', Nonprofit::class);
        yield MenuItem::linkToCrud('Focus', 'fas fa-folder-open', Focus::class);
        yield MenuItem::linkToCrud('Skill', 'fas fa-folder-open', Skill::class);
        yield MenuItem::linkToCrud('Representative', 'fa fa-folder-open', Person::class)
                        ->setController(RepresentativeCrudController::class);
        yield MenuItem::linkToCrud('Volunteer', 'fa fa-folder-open', Person::class)
                        ->setController(VolunteerCrudController::class);
        yield MenuItem::linktoRoute('Dashboard', 'fas fa-folder-open', 'dashboard');
        yield MenuItem::linktoRoute('Home', 'fas fa-home', 'home_page');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     *
     */
    public function dashboard(ChartService $charter)
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

}

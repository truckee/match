<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Focus;
use App\Entity\Nonprofit;
use App\Entity\Representative;
use App\Entity\Skill;
use App\Entity\Volunteer;
use App\Services\ChartService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardController extends AbstractDashboardController
{

    /**
     * @Route("/")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(NonprofitCrudController::class)->generateUrl());//        return parent::index();
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

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
                        ->setTitle('ConnectionsReno');
    }

    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return UserMenu::new()
                        ->displayUserAvatar(false);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Nonprofit', 'fas fa-folder-open', Nonprofit::class);
        yield MenuItem::linkToCrud('Focus', 'fas fa-folder-open', Focus::class);
        yield MenuItem::linkToCrud('Skill', 'fas fa-folder-open', Skill::class);
        yield MenuItem::linkToCrud('Staff', 'fas fa-folder-open', Representative::class);
        yield MenuItem::linkToCrud('Volunteer', 'fas fa-folder-open', Volunteer::class);
        yield MenuItem::linkToCrud('Admin', 'fas fa-folder-open', Admin::class);
        yield MenuItem::linktoRoute('Dashboard', 'fas fa-folder-open', 'dashboard');
        yield MenuItem::linktoRoute('Home', 'fas fa-folder-open', 'home_page');
    }
}

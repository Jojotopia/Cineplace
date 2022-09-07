<?php

namespace App\Controller\Admin;
setlocale(LC_TIME, 'fr_FR', "French");
date_default_timezone_set('Europe/Paris');

use App\Entity\Acteur;
use App\Entity\Categorie;
use App\Entity\Film;
use App\Entity\Projection;
use App\Entity\Realisateur;
use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    //Vérification que l'utilisateur qui vienx sur cette page est bien administrateur
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        session_start();
        if(isset($_SESSION['user'])){
            if($_SESSION['user']['type'] == "admin"){
                $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
                return $this->redirect($adminUrlGenerator->setController(FilmCrudController::class)->generateUrl());
            }
        }else{
            return $this->redirect("/");
        }
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Accueil', 'fa fa-home', '/');
        yield MenuItem::linkToCrud('Films', 'fas fa-film', Film::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Categorie::class);
        yield MenuItem::linkToCrud('Acteurs', 'fas fa-user', Acteur::class);
        yield MenuItem::linkToCrud('Réalisateurs', 'fas fa-user-tie', Realisateur::class);
        yield MenuItem::linkToCrud('Projections', 'fas fa-video', Projection::class);
        yield MenuItem::linkToCrud('Réservations', 'fas fa-ticket-alt', Reservation::class);
    }
}

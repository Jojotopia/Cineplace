<?php

namespace App\Controller;
setlocale(LC_TIME, 'fr_FR', "French");
date_default_timezone_set('Europe/Paris');

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    //Point d'entrer du site en VueJS suppression de la session potentiellement créer pour un administrateur
    //permettant d'éviter qu'en se déconnectant tout en laissant le navigateur ouvert une autre personne 
    //accède à l'espace admin
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        session_start();
        session_unset();
        session_destroy();
        return $this->render('app.html.twig', []);
    }
}

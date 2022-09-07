<?php

namespace App\Controller;
setlocale(LC_TIME, 'fr_FR', "French");
date_default_timezone_set('Europe/Paris');

use App\Entity\Client;
use App\Entity\Reservation;
use App\Repository\ClientRepository;
use App\Repository\ProjectionRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiRequestController extends AbstractController
{
    //Uniquement ici car pour se connecter via l'api il faut définir cette route
    #[Route(path: '/api/login_check', name: 'api_login', methods:['POST'])]
    public function apiLogin(){return null;}

    //Ajoute un client donné par un formulaire VueJS nécessite la vérification de la disponibilité de l'adresse mail
    #[Route('/api/register', name: 'api_register', methods:['POST'])]
    public function apiRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager){
        $user = new Client();
        try{
            $user->setEmail($request->toArray()["email"]);
            $user->setCivilite($request->toArray()["civilite"]);
            $user->setNom($request->toArray()["nom"]);
            $user->setPrenom($request->toArray()["prenom"]);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $request->toArray()["password"]
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return new Response();
        }catch(\Exception $e){
            throw new Exception("L'adresse mail est déjà utilisé !");
        }
    }

    //Modification d'un mot de passe client nécessite la vérification du mot de passe et la fonction de hashage
    #[Route('/api/update_client_password', name: 'update_client_password', methods:['POST'])]
    public function updatePassword(Request $request, ClientRepository $clientRepo, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        try{
            $user = $clientRepo->findOneBy(["email" => $request->toArray()['email']]);
        }catch(\Exception $e){
            throw new Exception("Votre compte n'a pas été trouvé !");
        }
        if($passwordHasher->isPasswordValid($user, $request->toArray()['passwordActuel'])){
            try{
                $hashedPassword = $passwordHasher->hashPassword($user, $request->toArray()['password']);
                $user->setPassword($hashedPassword);
                $em->flush();
                return new Response();
            }catch(\Exception $e){
                throw new Exception("Une erreur s'est produite !");
            }
        }else{
            throw new Exception("Le mot de passe actuel est erroné !");
        }
    }

    //La suppression d'un client nécessite la suppression de ses réservations et de redistribué ses places
    #[Route('/api/client_delete', name: 'api_client_delete', methods:['POST'])]
    public function apiClientDelete(Request $request, ClientRepository $clientRepo, ReservationRepository $reservationRepo, EntityManagerInterface $em): Response
    {
        try{
            $user = $clientRepo->findOneBy(["email" => $request->toArray()['email']]);
            $reservationUser = $reservationRepo->findBy(['client' => $user]);
            foreach($reservationUser as $r){
                $projection = $r->getProjection();
                $projection->setNbPlaceDispo($projection->getNbPlaceDispo()+$r->getNbSieges());
                $em->remove($r);
                $em->flush();
            }
            $em->remove($user);
            $em->flush();
            return new Response();
        }catch(\Exception $e){
            throw new Exception("Une erreur s'est produite lors de la suppression du compte !");
        }
    }

    //Création d'une réservation nécéssite le calcul des places restantes de la projection
    #[Route('/api/reserver', name: 'api_reserver', methods:['POST'])]
    public function apiReserver(Request $request, ClientRepository $clientRepo, ProjectionRepository $projectionRepo, EntityManagerInterface $em): Response
    {
        $client = $clientRepo->findOneBy(["email" => $request->toArray()['client']]);
        $reservation = new Reservation();
        $reservation->setClient($client);
        $reservation->setNbSieges($request->toArray()['nbSieges']);
        $reservation->setProjection($projectionRepo->find($request->toArray()['projection']));
        $projectionChoisie = $reservation->getProjection();
        if($projectionChoisie->getNbPlaceDispo() >= $reservation->getNbSieges()){
            $projectionChoisie->setNbPlaceDispo($projectionChoisie->getNbPlaceDispo()-$reservation->getNbSieges());
            $em->persist($reservation);
            $em->flush();
            return new Response();
        }else{
            throw new Exception("Il ne reste plus que ".$projectionChoisie->getNbPlaceDispo()." places disponibles pour cette séance !");
        }
    }

    //Suppression d'un réservation nécessite le calcul des places restantes pour la projection
    #[Route('/api/annuler_reservation', name: 'annuler_reservation', methods:['POST'])]
    public function apiAnnulerReservation(Request $request, ClientRepository $clientRepo, ReservationRepository $reservationRepo, EntityManagerInterface $em): Response
    {
        $client = $clientRepo->findOneBy(["email" => $request->toArray()['client']]);
        $reservation = $reservationRepo->find($request->toArray()['reservation']);
        if($client->getEmail() === $reservation->getClient()->getEmail()){
            if($reservation->getProjection()->getDate() > new DateTime()){
                $projection = $reservation->getProjection();
                $projection->setNbPlaceDispo($projection->getNbPlaceDispo()+$reservation->getNbSieges());
                $em->remove($reservation);
                $em->flush();
                return new Response();
            }else{
                throw new Exception("La projection à déjà eu lieu vous ne pouvez plus annuler votre billet !");
            }
        }else{
            throw new Exception("Cette réservation ne vous appartient pas !"."");
        }
    }

    //Gestion du passsage de VueJS à Symfony en ouvrant une session si l'utilisateur est un administrateur
    #[Route('/api/admin', name: 'api_admin', methods:['POST'])]
    public function apiAdmin(Request $request, JWTEncoderInterface $jwtEncoder, ClientRepository $clientRepo, ReservationRepository $reservationRepo, EntityManagerInterface $em): Response
    {
        session_start();
        if(isset($_SESSION['user'])){
            if($_SESSION['user']['type']=="admin"){
                return new Response();
            }
        }
        try {
            //Décodage du jeton envoyer par VueJS
            $jwtEncoder->decode($request->toArray()['token']);
            $tokenParts = explode(".", $request->toArray()['token']);  
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtPayload = json_decode($tokenPayload);
            //Si le role est admin on ouvre une session permettant de passer sur les pages Symfony EasyAdmin
            if(in_array("ROLE_ADMIN", $jwtPayload->roles)){
                $_SESSION['user']['type']="admin";
                $_SESSION['user']['login']=$jwtPayload->username;
                return new Response();
            }
            throw new Exception("Vous n'êtes pas administrateur !");
        } catch (JWTDecodeFailureException $ex) {
            throw new Exception("Connecter vous d'abord !");
        }
    }
}

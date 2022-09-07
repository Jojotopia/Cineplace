<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            IntegerField::new('nbSieges'),
            AssociationField::new('projection'),
            AssociationField::new('client'),
        ];
    }
    
    public function configureActions(Actions $actions): Actions
    {
        //Un administrateur ne peut pas modifier ou créer une réservation
        //Pour supprimer une réservation il devra supprimer la projection
        return $actions
            ->disable(Action::EDIT, Action::NEW, Action::DELETE)
        ;
    }
    
    //Vérification que l'utilisateur qui vienx sur cette page est bien administrateur
    public function index(AdminContext $context)
    {
        session_start();
        if(isset($_SESSION['user']) && $_SESSION['user']['type'] == "admin"){
            session_write_close();
            return parent::index($context);
        }else{
            return $this->redirect("/");
        }
    }
}

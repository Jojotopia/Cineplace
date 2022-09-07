<?php

namespace App\Controller\Admin;

use App\Entity\Projection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ProjectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Projection::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            IntegerField::new('nbPlaces'),
            //Le nombre de place disponible sera calculé avant l'ajout via un EventSubscriber
            IntegerField::new('nbPlaceDispo')->setDisabled(),
            DateTimeField::new('date')->setTimezone(date_default_timezone_get()),
            AssociationField::new('film'),
        ];
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

<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('nom'),
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

<?php

namespace App\Controller\Admin;

use App\Entity\Film;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FilmCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Film::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('titre'),
            TextareaField::new('resume')->onlyOnForms(),
            TextEditorField::new('resume')->onlyOnIndex(),
            TextField::new('affiche')->onlyOnIndex(),
            TextField::new('afficheFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('affiche')->setBasePath('/uploads/affiches/')->onlyOnIndex(),
            IntegerField::new('restriction_age'),
            TimeField::new('duree'),
            DateField::new('date_sortie'),
            AssociationField::new('categorie'),
            AssociationField::new('acteurs'),
            AssociationField::new('realisateurs'),
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

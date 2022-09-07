<?php 

namespace App\EventSubscriber;

use App\Entity\Projection;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    //Nombre de places avant la modification
    private $nbPlacesAvant;

    public static function getSubscribedEvents()
    {
        return [
            //Avant la modification et l'ajout d'une projection lancer la fonction setPlaceDispoAvant
            BeforeCrudActionEvent::class => ['setPlaceDispoAvant'],
            //Avant la modification d'une projection lancer la fonction setPlaceDispo
            BeforeEntityUpdatedEvent::class => ['setPlaceDispo'],
            //Avant l'ajout d'une projection lancer la fonction setPlaceDispoNew
            BeforeEntityPersistedEvent::class => ['setPlaceDispoNew'],
        ];
    }

    //Récupération du nombre de places possibles avant la modification
    public function setPlaceDispoAvant(BeforeCrudActionEvent $event){
        $entity = $event->getAdminContext()->getEntity()->getInstance();

        if (!($entity instanceof Projection)) {
            return;
        }

        $this->nbPlacesAvant = $entity->getNbPlaces();
    }

    //Pour une modification de projection
    public function setPlaceDispo(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Projection)) {
            return;
        }

        //Si le nouveau nombre de places est inférieur au nombre de places réservées, la modification ne sera pas effectué
        //et la projection récuperera le nombre de place avant modification
        if($entity->getNbPlaces() < ($this->nbPlacesAvant-$entity->getNbPlaceDispo())){
            $entity->setNbPlaces($this->nbPlacesAvant);
        }

        //Sinon le nouveau nombre de places disponibles sera actualisé
        $differencePlaces = ($entity->getNbPlaces()-$this->nbPlacesAvant);

        $entity->setNbPlaceDispo($entity->getNbPlaceDispo()+$differencePlaces);
    }

    //Pour un ajout de projection
    public function setPlaceDispoNew(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Projection)) {
            return;
        }

        //Le nombre de place disponibles est égal au nombre de places possibles
        $entity->setNbPlaceDispo($entity->getNbPlaces());
    }
}
<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
class PublishedDateEntitySubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return[
          KernelEvents::VIEW=> ['getDatePublished', EventPriorities::PRE_WRITE]
        ];
    }
    public function getDatePublished(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entity instanceof PublishedDateEntityInterface || Request::METHOD_POST !== $method){
            return;
        }

        $entity->setDate(new \DateTime());
    }
}
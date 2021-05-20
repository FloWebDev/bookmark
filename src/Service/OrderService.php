<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class OrderService
{
    private $em;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em       = $em;
    }

    public function refreshOrder($entities)
    {
        if (empty($entities[0])) {
            return;
        }

        if (!method_exists($entities[0]::class, 'setZ')) {
            throw new \Exception('Erreur sur refreshOrder');
        }

        foreach ($entities as $order => $entity) {
            $entity->setZ($order + 1);
        }

        $this->em->flush();
    }

    public function handleUpAndDownPosition($entity, $entities, $direction)
    {
        if (!method_exists($entity::class, 'getZ') || !method_exists($entity::class, 'setZ')
        || !method_exists($entities[0]::class, 'getZ') || !method_exists($entities[0]::class, 'setZ')) {
            throw new \Exception('Erreur sur handleUpAndDownPosition');
        }

        if ($direction === 'up') {
            $entityIndex = array_search($entity, $entities);
            if ($entityIndex !== false && isset($entities[$entityIndex - 1])) {
                $entities[$entityIndex - 1]->setZ($entity->getZ());
            }
            $entity->setZ($entity->getZ() - 1);
        } else {
            $entityIndex = array_search($entity, $entities);
            if ($entityIndex !== false && isset($entities[$entityIndex + 1])) {
                $entities[$entityIndex + 1]->setZ($entity->getZ());
            }
            $entity->setZ($entity->getZ() + 1);
        }

        $this->em->flush();
    }
}

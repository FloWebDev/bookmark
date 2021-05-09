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

    public function handleOrderZ($targetEntity, $entities, int $previousOlder)
    {
        if (!method_exists($targetEntity::class, 'getZ') || !method_exists($targetEntity::class, 'setZ')
        || !method_exists($entities[0]::class, 'getZ') || !method_exists($entities[0]::class, 'setZ')) {
            throw new \Exception('Erreur sur handleOrderZ');
        }
        $newZ = $targetEntity->getZ();

        if ($previousOlder > $newZ) {
            foreach ($entities as $p) {
                if ($p->getZ() <= $newZ && $p !== $targetEntity) {
                    $p->setZ($p->getZ() + 1);
                }
            }
        } elseif ($previousOlder < $newZ) {
            foreach ($entities as $p) {
                if ($p->getZ() <= $newZ && $p !== $targetEntity) {
                    $p->setZ($p->getZ() - 1);
                }
            }
        }
        $this->em->flush();
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
            dump('up');
            $entityIndex = array_search($entity, $entities);
            if ($entityIndex !== false && isset($entities[$entityIndex - 1])) {
                $entities[$entityIndex - 1]->setZ($entity->getZ());
            }
            $entity->setZ($entity->getZ() - 1);
        } else {
            dump('down');
            $entityIndex = array_search($entity, $entities);
            dump($entityIndex);
            if ($entityIndex !== false && isset($entities[$entityIndex + 1])) {
                dump($entities[$entityIndex + 1]);
                dump($entity->getZ());
                $entities[$entityIndex + 1]->setZ($entity->getZ());
            }
            $entity->setZ($entity->getZ() + 1);
        }

        $this->em->flush();
    }
}

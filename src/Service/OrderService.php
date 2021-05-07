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
        if (!method_exists($entities[0]::class, 'setZ')) {
            throw new \Exception('Erreur sur refreshOrder');
        }

        foreach ($entities as $order => $entity) {
            $entity->setZ($order + 1);
        }

        $this->em->flush();
    }
}

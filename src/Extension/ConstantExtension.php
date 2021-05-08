<?php

namespace App\Extension;

class ConstantExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    public function getName()
    {
        return 'constant_extension';
    }

    public function getGlobals(): array
    {
        $class     = new \ReflectionClass('App\Constant\Constant');
        $constants = $class->getConstants();

        return array(
            'Constant' => $constants
        );
    }
}

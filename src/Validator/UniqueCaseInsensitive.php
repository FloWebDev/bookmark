<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueCaseInsensitive extends Constraint
{
    public $message = '"{{ string }}" existe déjà.';
    public $field;
    public $currentValue;
}

<?php
namespace App\Validator;

use App\Repository\UserRepository;
use App\Validator\UniqueCaseInsensitive;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueCaseInsensitiveValidator extends ConstraintValidator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueCaseInsensitive) {
            throw new UnexpectedTypeException($constraint, UniqueCaseInsensitive::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ($value === $constraint->currentValue) {
            // S'il s'agit de la même valeur (avant/après) pour la même entité
            return;
        }

        if ($this->userRepository->findByLike($constraint->field, $value) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}

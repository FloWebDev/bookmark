<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CaptchaConstraintValidator extends ConstraintValidator
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CaptchaConstraint) {
            throw new UnexpectedTypeException($constraint, CaptchaConstraint::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_int($value) || is_null($this->session->get('captcha'))) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'integer');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        if ($value !== $this->session->get('captcha')) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ integer }}', $value)
                ->addViolation();
        }
    }
}

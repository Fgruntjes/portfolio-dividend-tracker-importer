<?php

namespace App\Validator;

use App\Broker\ExportConverter\ExportConverterRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BrokerExportConverterValidator extends ConstraintValidator
{
    private ExportConverterRegistry $brokerRegistry;

    public function __construct(ExportConverterRegistry $brokerRegistry)
    {
        $this->brokerRegistry = $brokerRegistry;
    }

    public function validate($value, BrokerExportConverter|Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($this->brokerRegistry->has($value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}

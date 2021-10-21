<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class BrokerExportConverter extends Constraint
{
    public $message = 'The broker "{{ value }}" does not exist.';
}

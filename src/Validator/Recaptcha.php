<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Recaptcha extends Constraint
{
    // error message to display if validation fails
    public string $message = 'Please confirm that you are not a robot (complete the CAPTCHA verification).';
}
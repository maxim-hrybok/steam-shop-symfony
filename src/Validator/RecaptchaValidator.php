<?php

namespace App\Validator;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecaptchaValidator extends ConstraintValidator
{
    public function __construct(
        private RequestStack $requestStack,
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'RECAPTCHA_SECRET_KEY')] 
        private string $secretKey
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        // 1. Get the reCAPTCHA response from the request
        $request = $this->requestStack->getCurrentRequest();
        $recaptchaResponse = $request->request->get('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        // 2. Send a request to Google's reCAPTCHA API to verify the response
        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->secretKey,
                'response' => $recaptchaResponse,
                'remoteip' => $request->getClientIp(),
            ]
        ]);

        // Convert the JSON response to an array
        $data = $response->toArray(false);

        // 3. Check the verification result
        if (!isset($data['success']) || $data['success'] !== true) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
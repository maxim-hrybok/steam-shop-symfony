<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginCaptchaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'RECAPTCHA_SECRET_KEY')] 
        private string $secretKey
    ) {}

    //  This method is required by the EventSubscriberInterface and tells Symfony which events this subscriber wants to listen to.
    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => 'onCheckPassport',
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        // We only want to check the CAPTCHA on the login route and only for POST requests (when the form is submitted).
        if ($request->attributes->get('_route') !== 'app_login' || !$request->isMethod('POST')) {
            return;
        }

        $recaptchaResponse = $request->request->get('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            // if the CAPTCHA response is empty, we throw an exception with a user-friendly message.
            throw new CustomUserMessageAuthenticationException('Please confirm that you are not a robot.');
        }

        // сheck the CAPTCHA response with Google's reCAPTCHA API
        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->secretKey,
                'response' => $recaptchaResponse,
                'remoteip' => $request->getClientIp(),
            ]
        ]);

        $data = $response->toArray(false);

        if (!isset($data['success']) || $data['success'] !== true) {
            throw new CustomUserMessageAuthenticationException('CAPTCHA verification failed. Please try again.');
        }
    }
}
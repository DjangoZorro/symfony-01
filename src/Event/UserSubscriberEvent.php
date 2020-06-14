<?php

namespace App\Event;

use App\Entity\UserPreferences;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriberEvent implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $defaultLocale;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Mailer $mailer, EntityManagerInterface $entityManager, string $defaultLocale)
    {
        $this->mailer = $mailer;
        $this->defaultLocale = $defaultLocale;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $preferences = new UserPreferences();
        $preferences->setLocale($this->defaultLocale);

        $user = $event->getRegisteredUser();
        $user->setPreferences($preferences);

        $this->entityManager->flush();

        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
    }
}
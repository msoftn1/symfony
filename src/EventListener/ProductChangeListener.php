<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Слушатель событий изменения/добавления товаров.
 */
class ProductChangeListener
{
    /** E-mail администратора. */
    private string $emailAdmin;

    /** E-mail для получения уведомлений. */
    private string $emailForNotifications;

    /**
     * Конструктор.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->emailAdmin = $container->getParameter("email_admin");
        $this->emailForNotifications = $container->getParameter("emain_for_notifications");
    }

    /**
     * Событие добавления.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $this->sendEmail(\sprintf("Товар %s добавлен.", $entity->getTitle()));
    }

    /**
     * Событие изменения.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $this->sendEmail(\sprintf("Товар %s обновлен.", $entity->getTitle()));
    }

    /**
     * Отправить e-mail
     *
     * @param $message
     */
    private function sendEmail($message): void
    {
        try {
            mail(
                $this->emailForNotifications,
                'Изменение каталога товаров',
                $message,
                [
                    'From' => $this->emailAdmin,
                    'X-Mailer' => 'PHP/' . phpversion()
                ]
            );
        } catch (\Exception $e) {
        }
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockLowNotification extends Notification
{
    use Queueable;

    protected $article;
    protected $seuil;

    /**
     * Create a new notification instance.
     */
    public function __construct($article, int $seuil)
    {
        $this->article = $article;
        $this->seuil   = $seuil;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Prioritize database notifications
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $articleName = $this->article->name;
        $currentQuantity = $this->article->quantite;
        $threshold = $this->seuil;
        // Assuming you have a route to view an article, e.g., 'articles.show'
        $articleUrl = route('articles.show', $this->article->id);

        return (new MailMessage)
                    ->subject("Alerte de stock bas pour l'article : {$articleName}")
                    ->greeting('Bonjour,')
                    ->line("Le stock pour l'article \"{$articleName}\" est bas.")
                    ->line("Quantité actuelle : {$currentQuantity} unités.")
                    ->line("Le seuil d'alerte était fixé à : {$threshold} unités.")
                    ->action('Voir l\'article', $articleUrl)
                    ->line('Veuillez prendre les mesures nécessaires pour réapprovisionner le stock.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message'     => "Le stock de l’article « {$this->article->name} » est désormais de {$this->article->quantite} unités (seuil = {$this->seuil}).",
            'article_id'  => $this->article->id,
            'current_qty' => $this->article->quantite,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

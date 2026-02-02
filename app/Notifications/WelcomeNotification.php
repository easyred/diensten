<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $role = ucfirst($notifiable->role ?? 'user');
        // Translate role to Dutch
        $roleTranslations = [
            'plumber' => 'Loodgieter',
            'gardener' => 'Tuinier',
            'client' => 'Klant',
        ];
        $roleDutch = $roleTranslations[$notifiable->role] ?? $role;
        
        return (new MailMessage)
            ->subject('Welkom bij diensten.pro!')
            ->greeting('Hallo ' . $notifiable->full_name . '!')
            ->line('Welkom bij diensten.pro! We zijn blij je aan boord te hebben.')
            ->line('Je account is succesvol aangemaakt.')
            ->line('**Je Accountgegevens:**')
            ->line('• Rol: ' . $roleDutch)
            ->line('• E-mail: ' . $notifiable->email)
            ->when($notifiable->address, function ($mail) use ($notifiable) {
                return $mail->line('• Adres: ' . $notifiable->address);
            })
            ->when($notifiable->company_name, function ($mail) use ($notifiable) {
                return $mail->line('• Bedrijf: ' . $notifiable->company_name);
            })
            ->line('Bezoek je dashboard om te beginnen:')
            ->action('Ga naar Dashboard', url('/dashboard'))
            ->line('Bedankt voor je aanmelding bij diensten.pro!');
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


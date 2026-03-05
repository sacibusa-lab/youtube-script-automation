<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $remaining,
        public float $total,
        public string $tokenType = 'script', // 'script' | 'image'
        public ?Plan $plan = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $percent     = $this->total > 0 ? round(($this->remaining / $this->total) * 100, 1) : 0;
        $label       = $this->tokenType === 'image' ? 'Image' : 'Script';
        $planName    = $this->plan?->name ?? 'your current';
        $upgradeUrl  = url('/payment/initialize');

        return (new MailMessage)
            ->subject("⚠️ Low {$label} Token Balance — {$percent}% Remaining")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your **{$label} Token** balance on the **{$planName}** plan is running low.")
            ->line("**Remaining:** " . number_format($this->remaining) . " tokens ({$percent}% left)")
            ->when($percent <= 5, fn($mail) => $mail->line('🚨 You have less than 5% remaining. Further generations may be blocked soon.'))
            ->when($percent <= 20 && $percent > 5, fn($mail) => $mail->line('⚠️ You have used over 80% of your monthly allocation.'))
            ->action('Upgrade Your Plan', $upgradeUrl)
            ->line('If you need more tokens right away, consider upgrading to a higher plan.')
            ->salutation('— The Script AI Team');
    }
}

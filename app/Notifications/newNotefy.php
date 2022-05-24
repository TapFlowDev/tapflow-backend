<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class newNotefy extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }
    public function via($notifiable) 
    {
         return ['database', 'broadcast'];
    }
    public function toArray($notifiable)
    {
         return ['comment' => 'asd test'];
          
        
     }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
    public function broadcastType()
    {
        return 'new-comment';
    }
}

<?php

namespace App\Notifications;

use App\Models\ForumReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForumReplyPosted extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;
    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public ForumReply $reply,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $thread = $this->reply->thread;
        $category = $thread->category;
        $url = route('forum.threads.show', [$category, $thread]).'#reply-'.$this->reply->id;

        return (new MailMessage)
            ->subject($this->reply->author->name.' replied in "'.$thread->title.'"')
            ->view('emails.forum-reply', [
                'reply' => $this->reply,
                'thread' => $thread,
                'url' => $url,
            ]);
    }
}

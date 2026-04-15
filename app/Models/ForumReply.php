<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'forum_thread_id',
        'user_id',
        'parent_id',
        'body',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function descendantCount(): int
    {
        return $this->children->sum(fn (self $child): int => 1 + $child->descendantCount());
    }

    public function containsReplyInTree(int $replyId): bool
    {
        if ($this->id === $replyId) {
            return true;
        }

        return $this->children->contains(fn (self $child): bool => $child->containsReplyInTree($replyId));
    }
}

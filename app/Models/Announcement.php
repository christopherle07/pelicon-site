<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_url',
        'embed_url',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AnnouncementComment::class)->latest();
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function embedFrameUrl(): ?string
    {
        if (blank($this->embed_url)) {
            return null;
        }

        $url = trim((string) $this->embed_url);
        $host = Str::lower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            return 'https://www.youtube.com/embed/'.$path;
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com'], true)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

            if (filled($query['v'] ?? null)) {
                return 'https://www.youtube.com/embed/'.$query['v'];
            }

            if (Str::startsWith($path, 'embed/')) {
                return 'https://www.youtube.com/'.$path;
            }

            if (Str::startsWith($path, 'shorts/')) {
                return 'https://www.youtube.com/embed/'.Str::after($path, 'shorts/');
            }
        }

        if (in_array($host, ['vimeo.com', 'www.vimeo.com'], true) && $path !== '') {
            return 'https://player.vimeo.com/video/'.$path;
        }

        if (in_array($host, ['player.vimeo.com', 'www.player.vimeo.com'], true) && Str::startsWith($path, 'video/')) {
            return 'https://player.vimeo.com/'.$path;
        }

        return null;
    }
}

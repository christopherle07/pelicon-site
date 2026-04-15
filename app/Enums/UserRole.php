<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Moderator = 'moderator';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'User',
            self::Moderator => 'Mod',
            self::Admin => 'Admin',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::User => 'Community member.',
            self::Moderator => 'This user is a site Moderator. Site Moderators moderates and answer questions in forum threads.',
            self::Admin => 'This user is a site Admin. Site Admins manages the website, responds to forum threads, and publishes important announcements.',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::User => '',
            self::Moderator => 'staff-badge--moderator',
            self::Admin => 'staff-badge--admin',
        };
    }

    public function isStaff(): bool
    {
        return $this === self::Moderator || $this === self::Admin;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentAttachment extends Model
{
    protected $fillable = [
        'assignment_id',
        'uploaded_by',
        'path',
        'original_name',
        'mime',
        'size',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function url(): string
    {
        // Root-relative (/storage/...) agar mengikuti host:port halaman yang sedang dibuka
        // (mis. php artisan serve di :8000), bukan absolut dari APP_URL.
        return '/storage/' . ltrim(str_replace('\\', '/', $this->path), '/');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 1) . ' MB';
    }
}

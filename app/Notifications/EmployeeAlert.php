<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi in-app generik untuk karyawan (disimpan ke channel database).
 *
 * Tipe: assigned | revision | deadline
 */
class EmployeeAlert extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public ?int $assignmentId = null,
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => $this->type,
            'title'         => $this->title,
            'message'       => $this->message,
            'assignment_id' => $this->assignmentId,
            'url'           => $this->url,
        ];
    }
}

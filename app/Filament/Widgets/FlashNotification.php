<?php

namespace App\Filament\Widgets;

use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class FlashNotification extends Widget
{
    protected static bool $isLazy = false;
    protected static bool $isDiscovered = true;
    protected string $view = 'filament.widgets.flash-notification';
    protected static ?int $sort = -99;

    public function mount(): void
    {
        if (session('success')) {
            Notification::make()
                ->title(session('success'))
                ->success()
                ->send();
        }

        if (session('error')) {
            Notification::make()
                ->title(session('error'))
                ->danger()
                ->send();
        }

        if (session('warning')) {
            Notification::make()
                ->title(session('warning'))
                ->warning()
                ->send();
        }

        if (session('info')) {
            Notification::make()
                ->title(session('info'))
                ->info()
                ->send();
        }
    }
}

<?php

namespace App\Filament\App\Resources\TransferResource\Pages;

use App\Filament\App\Resources\TransferResource;
use App\ValueObjects\MoneyValue;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransfer extends ViewRecord
{
    protected static string $resource = TransferResource::class;

    public function getTitle(): string
    {
        return 'Repasse #' . $this->getRecord()->id;
    }

    public function getSubheading(): string
    {
        return $this->getRecord()->owner->user->name  . ' - '
            . Carbon::parse($this->getRecord()->transferred_at)->format('d/m/Y');
    }
}

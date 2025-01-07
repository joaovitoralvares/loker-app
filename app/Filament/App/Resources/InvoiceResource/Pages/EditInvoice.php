<?php

namespace App\Filament\App\Resources\InvoiceResource\Pages;

use App\Actions\PayInvoice;
use App\Enum\ContractInvoiceStatusEnum;
use App\Filament\App\Resources\InvoiceResource;
use App\Models\Invoice;
use App\ValueObjects\MoneyValue;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected $listeners = [
        'refreshInvoice' => 'refreshForm',
    ];

    public function refreshForm(): void
    {
        $this->getRecord()->refresh();
        $this->form->fill($this->record->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pay')->label('Pagar')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Invoice $invoice, Actions\Action $action) {
                    DB::transaction(function () use ($invoice, $action) {
                        $payInvoice = app(PayInvoice::class);
                        $payInvoice->execute($invoice);
                        $action->success();
                    });
                })->successRedirectUrl(ViewInvoice::getUrl(
                    ['record' => $this->getRecord()]
                ))
            ->visible(fn () => $this->record->status !== ContractInvoiceStatusEnum::PAID->value),
            Actions\Action::make('close')->label('Fechar fatura')
            ->color('info')
            ->requiresConfirmation()
            ->action(function (Invoice $invoice, Actions\Action $action) {
                $invoice->status = ContractInvoiceStatusEnum::PENDING->value;
                $invoice->save();
                $action->success();
            })->visible(fn () => $this->record->status === ContractInvoiceStatusEnum::DRAFT->value),
            Actions\DeleteAction::make(),
        ];
    }

}

<?php

namespace App\Actions;

use App\Enum\ContractInvoiceStatusEnum;
use App\Enum\OwnerTransactionStatusEnum;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Owner;
use Carbon\Carbon;

class PayInvoice
{
    public function execute(Invoice $invoice): void
    {
        $invoice->status = ContractInvoiceStatusEnum::PAID->value;
        $invoice->payment_date = Carbon::now();
        $invoice->save();
        $invoice->load('contract.owner');

        /** @var Contract $contract */
        $contract = $invoice->contract;
        /** @var Owner $owner */
        $owner = $contract->owner;

        $ownerTransactionAmount = $this->calculateOwnerTransactionAmount($owner->take_rate, $invoice->amount);
        $owner->transactions()->create([
            'amount' => $ownerTransactionAmount,
            'status' => OwnerTransactionStatusEnum::PENDING->value,
            'invoice_id' => $invoice->id,
            'company_id' => $owner->company_id,
        ]);
    }

    private function calculateOwnerTransactionAmount(float $takeRate, int $invoiceAmount): int
    {
        $ownerPercentage = 1 - $takeRate;

        return round(($invoiceAmount / 100 * $ownerPercentage), 2) * 100;
    }

}

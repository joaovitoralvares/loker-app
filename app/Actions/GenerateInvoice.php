<?php

namespace App\Actions;

use App\Enum\ContractInvoiceStatusEnum;
use App\Enum\ContractInvoiceTransactionTypeEnum;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Support\Carbon;

class GenerateInvoice
{
    public function execute(Contract $contract, Carbon $startDate): Invoice
    {
        $contract->load('vehicle.category');
        $dailyAmount = $contract->vehicle->category->daily_price;

        $startDate->startOfDay();
        $endDate = $startDate->isSunday()
            ? $startDate->copy()->endOfDay()
            : $startDate->copy()->next(Carbon::SUNDAY)->endOfDay();

        $dueDate = $endDate->copy()->addDays(7);
        $totalDays = (int) round($endDate->diffInDays($startDate, true));
        $amount = $totalDays * $dailyAmount;

        /** @var Invoice $invoice */
        $invoice = $contract->invoices()->create([
            'company_id' => $contract->company_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'due_date' => $dueDate,
            'status' => ContractInvoiceStatusEnum::DRAFT->value,
            'amount' => $amount,
        ]);

        $invoice->transactions()->create([
            'company_id' => $contract->company_id,
            'amount' => $amount,
            'type' => ContractInvoiceTransactionTypeEnum::DEBIT->value,
            'description' => $totalDays . 'X R$ ' . $dailyAmount / 100
        ]);

        return $invoice;
    }
}

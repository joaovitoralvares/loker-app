<?php

namespace App\Actions;

use App\Actions\SecurityDepositAccount\ScheduleWithdrawal;
use App\Enum\ContractInvoiceStatusEnum;
use App\Enum\ContractStatusEnum;
use App\Models\Contract;
use App\Models\SecurityDepositAccount;

class FinishContract
{
    public function __construct(private readonly ScheduleWithdrawal $scheduleWithdrawal)
    {
    }
    public function execute(Contract $contract)
    {
        $existsOpenInvoices = $contract->invoices()
            ->where('status', ContractInvoiceStatusEnum::DRAFT->value)
            ->exists();

        if ($existsOpenInvoices) {
            throw new \DomainException('all invoices need to be closed');
        }

        $contract->invoices()
            ->whereIn('status', [
                ContractInvoiceStatusEnum::PENDING->value,
                ContractInvoiceStatusEnum::OVERDUE->value
            ])->update([
                'status' => ContractInvoiceStatusEnum::PAID->value,
                'payment_date' => now()
            ]);

        $contract->load('customer.securityDepositAccount');
        /** @var SecurityDepositAccount $securityDepositAccount */
        $securityDepositAccount = $contract->customer->securityDepositAccount;

        $amountToBeReturned = $securityDepositAccount
            ->transactions()
            ->where('contract_id', $contract->id)
            ->sum('amount');

        if ($amountToBeReturned > 0) {
            $this->scheduleWithdrawal->execute([
                'account' => $securityDepositAccount,
                'amount' => $amountToBeReturned,
                'date' => now()->addDays(15)->endOfDay(),
                'contract_id' => $contract->id
            ]);
        }

        $contract->status = ContractStatusEnum::FINISHED->value;
        $contract->save();
    }

}

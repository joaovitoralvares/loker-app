<?php

namespace App\Actions;

use _PHPStan_e6dc705b2\Nette\InvalidArgumentException;
use App\Enum\ContractInvoiceStatusEnum;
use App\Enum\ContractInvoiceTransactionTypeEnum;
use App\Models\Invoice;
use App\Models\Transaction;

class DeleteTransaction
{
    public function execute(Transaction $transaction): Transaction
    {
        $transaction->load('invoice');
        $invoice = $transaction->invoice;

        if ($invoice->status !== ContractInvoiceStatusEnum::DRAFT->value) {
            throw new \DomainException('cannot delete transaction, invoice is not DRAFT');
        }

        $invoice->decrement('amount', $transaction->amount);
        $transaction->delete();

        return $transaction;
    }
}

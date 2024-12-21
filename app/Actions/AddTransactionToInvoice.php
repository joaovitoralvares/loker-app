<?php

namespace App\Actions;

use _PHPStan_e6dc705b2\Nette\InvalidArgumentException;
use App\Enum\ContractInvoiceTransactionTypeEnum;
use App\Models\Invoice;
use App\Models\Transaction;

class AddTransactionToInvoice
{
    /**
     * @param array{
     *     description: ?string,
     *     amount: int,
     *     invoice_id: int,
     *     type: string,
     * } $data
     * @return Transaction
     */
    public function execute(array $data): Transaction
    {
        if ($data['amount'] <= 0) {
            throw new InvalidArgumentException('amount must be greater than 0');
        }

        $amount = $data['amount'];
        if ($data['type'] === ContractInvoiceTransactionTypeEnum::CREDIT->value) {
            $amount *= -1;
        }

        $invoice = Invoice::query()->findOrFail($data['invoice_id']);

        $transaction = $invoice->transactions()->create([
            'description' => $data['description'] ?? null,
            'amount' => $amount,
            'company_id' => $invoice->company_id,
            'type' => ContractInvoiceTransactionTypeEnum::from($data['type'])->value,
        ]);

        $invoice->increment('amount', $amount);

        return $transaction;
    }
}

<?php

namespace App\Actions\SecurityDepositAccount;

use App\Enum\SecurityDepositTransactionStatusEnum;
use App\Enum\SecurityDepositTransactionTypeEnum;
use App\Models\SecurityDepositAccount;
use App\Models\SecurityDepositTransaction;

class MakeWithdrawal
{

    /**
     * @param array{
     *     account: SecurityDepositAccount,
     *     amount: int,
     *     contract_id: int,
     * } $data
     * @return SecurityDepositTransaction
     */
    public function execute(array $data): SecurityDepositTransaction
    {
        if ($data['amount'] <= 0) {
            throw new \DomainException('Amount must be greater than 0');
        }

        $description = 'Pagamento de pendência do contrato #' . $data['contract_id'];
        /** @var SecurityDepositAccount $account */
        $account = $data['account'];

        if ($account->transactions()->where('contract_id', $data['contract_id'])->sum('amount') < $data['amount']) {
            throw new \DomainException('Insufficient funds');
        }

        return $account->transactions()->create([
            'amount' => $data['amount'] * (-1),
            'type' => SecurityDepositTransactionTypeEnum::DEBIT,
            'description' => $description,
            'status' => SecurityDepositTransactionStatusEnum::PAID,
            'date' => now(),
            'contract_id' => $data['contract_id'],
        ]);
    }
}

<?php

namespace App\Actions\SecurityDepositAccount;

use App\Enum\SecurityDepositTransactionTypeEnum;
use App\Models\SecurityDepositAccount;
use App\Models\SecurityDepositTransaction;

class MakeDeposit
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

        $description = 'Depósito referente ao contrato #' . $data['contract_id'];
        $account = $data['account'];

        return $account->transactions()->create([
            'amount' => $data['amount'],
            'type' => SecurityDepositTransactionTypeEnum::CREDIT,
            'description' => $description,
            'contract_id' => $data['contract_id'],
        ]);
    }
}

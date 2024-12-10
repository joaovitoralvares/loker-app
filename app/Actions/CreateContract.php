<?php

namespace App\Actions;

use App\Actions\SecurityDepositAccount\MakeDeposit;
use App\Enum\ContractStatusEnum;
use App\Enum\ContractTypeEnum;
use App\Enum\VehicleStatusEnum;
use App\Models\Contract;
use App\Models\SecurityDepositAccount;
use App\Models\Vehicle\Vehicle;
use Illuminate\Support\Facades\DB;

readonly class CreateContract
{
    public function __construct(private MakeDeposit $makeDeposit)
    {}

    /**
     * @param array{
     *     company_id: int,
     *     owner_id: int,
     *     customer_id: int,
     *     vehicle_id: int,
     *     start_date: string,
     *     auto_renew: bool,
     *     security_deposit_amount: int,
     * } $data
     * @return Contract
     */
    public function execute(array $data): Contract
    {
        $contract = new Contract();
        $contract->company()->associate($data['company_id']);
        $contract->owner()->associate($data['owner_id']);
        $contract->customer()->associate($data['customer_id']);
        $contract->vehicle()->associate($data['vehicle_id']);
        $contract->start_date = $data['start_date'];
        $contract->auto_renew = $data['auto_renew'];
        $contract->status = ContractStatusEnum::ACTIVE;
        $contract->type = ContractTypeEnum::WEEKLY;

        $vehicle = Vehicle::query()->find($data['vehicle_id']);

        $vehicle->status = VehicleStatusEnum::RENTED->value;

        DB::transaction(function () use ($data, $contract, $vehicle) {
            $contract->save();
            $vehicle->save();

            $securityDepositAccount = SecurityDepositAccount::query()
                ->firstOrCreate([
                    'company_id'  => $data['company_id'],
                    'customer_id' => $data['customer_id'],
                ]);

            $this->makeDeposit->execute([
                'account' => $securityDepositAccount,
                'amount' => $data['security_deposit_amount'],
                'contract_id' => $contract->id,
            ]);
        });

        return $contract;
    }
}

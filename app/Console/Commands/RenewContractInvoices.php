<?php

namespace App\Console\Commands;

use App\Actions\GenerateInvoice;
use App\Enum\ContractStatusEnum;
use App\Models\Contract;
use Illuminate\Console\Command;

class RenewContractInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:renew-contract-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for each active contract with auto renew enabled';

    /**
     * Execute the console command.
     */
    public function handle(GenerateInvoice $generateInvoice)
    {
        $contracts = Contract::query()
            ->where('status', ContractStatusEnum::ACTIVE->value)
            ->where('auto_renew', true)
            ->get();

        foreach ($contracts as $contract) {
            $generateInvoice->execute($contract, now());
        }
    }
}

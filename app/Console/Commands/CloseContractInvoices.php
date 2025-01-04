<?php

namespace App\Console\Commands;

use App\Enum\ContractInvoiceStatusEnum;
use App\Models\Invoice;
use Illuminate\Console\Command;

class CloseContractInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-contract-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Invoice::query()->where('status', ContractInvoiceStatusEnum::DRAFT->value)
            ->where('end_date', '<=', now())
            ->update(['status' => ContractInvoiceStatusEnum::PENDING->value]);
    }
}

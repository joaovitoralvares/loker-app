<?php

namespace App\Console\Commands;

use App\Enum\ContractInvoiceStatusEnum;
use App\Models\Invoice;
use Illuminate\Console\Command;

class OverdueContractInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:overdue-contract-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Invoice::query()->where('status', ContractInvoiceStatusEnum::PENDING->value)
            ->where('due_date', '<=', now())
            ->update(['status' => ContractInvoiceStatusEnum::OVERDUE->value]);
    }
}

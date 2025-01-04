<?php

use App\Console\Commands\CloseContractInvoices;
use App\Console\Commands\OverdueContractInvoices;
use Illuminate\Support\Facades\Schedule;

Schedule::command(\App\Console\Commands\RenewContractInvoices::class)
    ->weekly()
    ->mondays()
    ->at('00:00');

Schedule::command(CloseContractInvoices::class)->dailyAt('00:00');
Schedule::command(OverdueContractInvoices::class)->dailyAt('00:00');

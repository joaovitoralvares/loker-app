<?php

namespace App\Filament\App\Resources\ContractResource\RelationManagers;

use App\Filament\App\Resources\InvoiceResource;
use App\Filament\App\Resources\InvoiceResource\Pages\EditInvoice;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Faturas';
    protected static ?string $label = 'Fatura';
    protected static ?string $pluralLabel = 'Faturas';

    public function form(Form $form): Form
    {
        return InvoiceResource::form($form);
    }

    public function table(Table $table): Table
    {
        return InvoiceResource::table($table);
    }
}

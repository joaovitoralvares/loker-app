<?php

namespace App\Filament\App\Resources\ContractResource\Pages;

use App\Actions\DeleteTransaction;
use App\Actions\FinishContract;
use App\Actions\SecurityDepositAccount\MakeWithdrawal;
use App\Actions\SecurityDepositAccount\ScheduleWithdrawal;
use App\Enum\ContractStatusEnum;
use App\Filament\App\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('finish')
                ->label('Finalizar')
                ->requiresConfirmation()
                ->action(function (Actions\Action $action) {
                    try {
                        DB::beginTransaction();
                        $finishContract = app(FinishContract::class);
                        $finishContract->execute($action->getRecord());
                        $action->successNotification(
                            Notification::make()
                            ->title('Finalizado com sucesso!')
                            ->success()
                        );
                        $action->success();
                        DB::commit();
                    } catch (\Throwable $exception) {
                        DB::rollBack();
                        $action->failureNotification(
                            Notification::make()
                                ->title($exception->getMessage())
                                ->danger()
                        );
                        $action->failure();
                        return;
                    }
                })
                ->extraModalFooterActions([
                    Actions\Action::make('finish_and_pay_with_security_deposit')
                    ->label('Finalizar e abater do caução')
                    ->form(function (Form $form) {
                        return $form->schema([
                            TextInput::make('amount')
                                ->label('Valor pago com caução')
                                ->numeric()
                                ->default(($this->getRecord()->pendingPaymentInvoices()->sum('amount') ?? 0) / 100)
                                ->dehydrateStateUsing(fn ($state) => $state * 100)
                        ]);
                    })->action(function (Actions\Action $action, $data) {
                            try {
                                DB::beginTransaction();
                                $makeWithdrawal = app(MakeWithdrawal::class);
                                $finishContract = app(FinishContract::class);

                                $makeWithdrawal->execute([
                                    'account' => $this->getRecord()->customer->securityDepositAccount,
                                    'amount' => $data['amount'],
                                    'contract_id' => $this->getRecord()->id,
                                ]);
                                $finishContract->execute($this->getRecord());
                                $action->successNotification(
                                    Notification::make()
                                        ->title('Finalizado com sucesso!')
                                        ->success()
                                );
                                $action->success();
                                DB::commit();
                            } catch (\Throwable $exception) {
                                DB::rollBack();
                                $action->failureNotification(
                                    Notification::make()
                                        ->title($exception->getMessage())
                                        ->danger()
                                );
                                $action->failure();
                                return;
                            }
                        })->cancelParentActions()
                ])->hidden($this->record->status === ContractStatusEnum::FINISHED->value),
            Actions\DeleteAction::make(),
        ];
    }
}

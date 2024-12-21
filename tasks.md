# criar um contrato

- selecionar veiculo (apenas veiculos disponiveis), investidor e cliente
- Selecionar o tipo do contrato (A principo apenas semanal)
- Autorenovação (ativo/desativado)
- Data de inicio do contrato
- Dia de finalização (exemplo, contrato semanal finaliza toda segunda)
- Valor de caução, prazo para devolução do caução apos termino do contrato.

# CONTRATO CRIADO

- registra o caução
- veiculo fica com status LOCADO



# contracts
- durationType (weekly)
- autoRenew: (true, false)
- customer_id
- vehicle_id
- status (ACTIVE, FINISHED)

# contract_invoices
- start_date
- end_date
- due_date
- amount
- contract_id
- status

# security_deposit_account
- balance
- user_id

# security_deposit_account_transactions
- account_id
- type (credit, debit)
- amount
- description
- contract_id

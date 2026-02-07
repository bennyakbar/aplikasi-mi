<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create journal entry from payment
     */
    public function createPaymentJournal(Payment $payment): JournalEntry
    {
        return DB::transaction(function () use ($payment) {
            // Get accounts
            $cashAccount = Account::where('code', '1110')->first(); // Kas Tunai
            $bankAccount = Account::where('code', '1120')->first(); // Bank
            $revenueAccount = Account::where('code', '4100')->first(); // Pendapatan SPP

            $debitAccount = $payment->payment_method === 'cash' ? $cashAccount : $bankAccount;

            // Create journal entry
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $payment->payment_date,
                'description' => "Penerimaan pembayaran - {$payment->receipt_number} - {$payment->student->name}",
                'user_id' => auth()->id(),
                'payment_id' => $payment->id,
                'status' => 'posted',
            ]);

            // Debit Cash/Bank
            $entry->lines()->create([
                'account_id' => $debitAccount->id,
                'debit' => $payment->paid_amount,
                'credit' => 0,
                'description' => "Penerimaan dari {$payment->student->name}",
            ]);

            // Credit Revenue
            $entry->lines()->create([
                'account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => $payment->paid_amount,
                'description' => "Pendapatan SPP - {$payment->student->name}",
            ]);

            return $entry;
        });
    }

    /**
     * Get general ledger for an account
     */
    public function getGeneralLedger(Account $account, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = $account->journalLines()
            ->with(['journalEntry'])
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted');
                if ($startDate) {
                    $q->where('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('entry_date', '<=', $endDate);
                }
            })
            ->orderBy('created_at');

        $lines = $query->get();
        $runningBalance = 0;
        $result = [];

        foreach ($lines as $line) {
            // Calculate running balance based on account type
            if (in_array($account->type, ['asset', 'expense'])) {
                $runningBalance += $line->debit - $line->credit;
            } else {
                $runningBalance += $line->credit - $line->debit;
            }

            $result[] = [
                'date' => $line->journalEntry->entry_date,
                'entry_number' => $line->journalEntry->entry_number,
                'description' => $line->description ?: $line->journalEntry->description,
                'debit' => $line->debit,
                'credit' => $line->credit,
                'balance' => $runningBalance,
            ];
        }

        return $result;
    }

    /**
     * Get monthly summary
     */
    public function getMonthlySummary(int $year, int $month): array
    {
        $startDate = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $revenue = Account::where('type', 'revenue')
            ->get()
            ->sum(fn($account) => $account->getBalance($startDate, $endDate));

        $expense = Account::where('type', 'expense')
            ->get()
            ->sum(fn($account) => $account->getBalance($startDate, $endDate));

        $cashBalance = Account::where('code', '1100')
            ->first()
                ?->getBalance(null, $endDate) ?? 0;

        return [
            'period' => "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT),
            'revenue' => $revenue,
            'expense' => $expense,
            'net_income' => $revenue - $expense,
            'cash_balance' => $cashBalance,
        ];
    }

    /**
     * Get trial balance
     */
    public function getTrialBalance(?string $asOfDate = null): array
    {
        $accounts = Account::where('is_active', true)
            ->orderBy('code')
            ->get();

        $result = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $account->getBalance(null, $asOfDate);

            if (abs($balance) < 0.01) {
                continue; // Skip zero balances
            }

            $debit = 0;
            $credit = 0;

            if (in_array($account->type, ['asset', 'expense'])) {
                $debit = $balance > 0 ? $balance : 0;
                $credit = $balance < 0 ? abs($balance) : 0;
            } else {
                $credit = $balance > 0 ? $balance : 0;
                $debit = $balance < 0 ? abs($balance) : 0;
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $result[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return [
            'items' => $result,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Chart of Accounts
     */
    public function accounts(): View
    {
        $accounts = Account::with('parent')
            ->orderBy('code')
            ->get();

        return view('accounting.accounts', compact('accounts'));
    }

    /**
     * Journal Entries (Jurnal Umum)
     */
    public function journal(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $entries = JournalEntry::with(['lines.account', 'user', 'payment'])
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->where('status', 'posted')
            ->orderBy('entry_date', 'desc')
            ->paginate(20);

        return view('accounting.journal', compact('entries', 'startDate', 'endDate'));
    }

    /**
     * General Ledger (Buku Besar)
     */
    public function ledger(Request $request): View
    {
        $accounts = Account::where('is_active', true)
            ->orderBy('code')
            ->get();

        $selectedAccount = null;
        $ledgerData = [];

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if ($request->filled('account_id')) {
            $selectedAccount = Account::find($request->account_id);
            if ($selectedAccount) {
                $ledgerData = $this->accountingService->getGeneralLedger(
                    $selectedAccount,
                    $startDate,
                    $endDate
                );
            }
        }

        return view('accounting.ledger', compact('accounts', 'selectedAccount', 'ledgerData', 'startDate', 'endDate'));
    }

    /**
     * Monthly Summary (Rekap Bulanan)
     */
    public function monthlySummary(Request $request): View
    {
        $year = $request->get('year', now()->year);
        $summaries = [];

        for ($month = 1; $month <= 12; $month++) {
            $summaries[$month] = $this->accountingService->getMonthlySummary($year, $month);
        }

        return view('accounting.monthly-summary', compact('summaries', 'year'));
    }

    /**
     * Trial Balance (Neraca Saldo)
     */
    public function trialBalance(Request $request): View
    {
        $asOfDate = $request->get('as_of', now()->format('Y-m-d'));
        $trialBalance = $this->accountingService->getTrialBalance($asOfDate);

        return view('accounting.trial-balance', compact('trialBalance', 'asOfDate'));
    }
}

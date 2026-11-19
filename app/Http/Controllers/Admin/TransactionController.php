<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_from.date' => 'Tanggal awal tidak valid.',
            'date_to.date' => 'Tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $query = Transaction::with('items')
            ->when($request->filled('code'), fn ($q) => $q->where('code', 'like', '%'.$request->code.'%'))
            ->when($request->filled('cashier'), fn ($q) => $q->where('cashier_name', 'like', '%'.$request->cashier.'%'))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $summaryCount = (clone $query)->where('status', 'success')->count();
        $summaryTotal = (clone $query)->where('status', 'success')->sum('total');

        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.transactions.index', compact('transactions', 'summaryCount', 'summaryTotal'));
    }
}
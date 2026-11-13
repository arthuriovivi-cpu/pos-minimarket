<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class ReceiptController extends Controller
{
    public function show(Transaction $transaction)
    {
        $user = auth()->user();

        // Admin boleh melihat semua struk, kasir hanya struk miliknya
        abort_unless(
            $user->isAdmin() || $transaction->user_id === $user->id,
            403,
            'Anda tidak memiliki akses ke struk ini.'
        );

        $transaction->load('items');

        return view('receipt', compact('transaction'));
    }
}
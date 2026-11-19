<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses with summary stats.
     */
    public function index(Request $request) // <--- ADD Request $request HERE
    {
        // 1. Initialize the query builder
        $query = Expense::query();

        // 2. Now $request is defined and can be used to filter
        if ($request->filled('category') && $request->category !== 'All Categories') {
            $query->where('category', $request->category);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Apply sorting and execute
        $expenses = $query->orderBy('date', 'desc')->get();

        $totalExpenses = $expenses->sum('amount');
        $totalEntries = $expenses->count();
        $averageExpense = $totalEntries > 0 ? $totalExpenses / $totalEntries : 0;

        $expensesByCategory = $expenses->groupBy('category')->map(function ($row) {
            return $row->sum('amount');
        });

        return view('admin.expenses', compact(
            'expenses',
            'totalExpenses',
            'totalEntries',
            'averageExpense',
            'expensesByCategory'
        ));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_url' => 'nullable|url',
        ]);

        Expense::create($validatedData);

        return redirect()->route('admin.expenses')
            ->with('success', 'Expense added successfully!');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validatedData = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_url' => 'nullable|url',
        ]);

        $expense->update($validatedData);

        return redirect()->route('admin.expenses')
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses')
            ->with('success', 'Expense deleted successfully!');
    }
}
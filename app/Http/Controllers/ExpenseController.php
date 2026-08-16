<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Accommodation;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Expense::class);
        $expenses = Expense::with(['expenseCategory', 'accommodation'])->latest()->paginate(15);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $this->authorize('create', Expense::class);
        $categories = ExpenseCategory::all();
        $accommodations = Accommodation::all();
        return view('expenses.create', compact('categories', 'accommodations'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $this->authorize('create', Expense::class);
        
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['category'] = ExpenseCategory::find($data['expense_category_id'])->slug;

        Expense::create($data);
        return redirect()->route('expenses.index')->with('success', 'Gasto registrado.');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        $categories = ExpenseCategory::all();
        $accommodations = Accommodation::all();
        return view('expenses.edit', compact('expense', 'categories', 'accommodations'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        
        $data = $request->validated();
        $data['category'] = ExpenseCategory::find($data['expense_category_id'])->slug;

        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado.');
    }
}

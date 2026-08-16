<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class ExpenseCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', ExpenseCategory::class);
        $categories = ExpenseCategory::withCount('expenses')->orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('expense_categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', ExpenseCategory::class);
        return view('expense_categories.create');
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        $this->authorize('create', ExpenseCategory::class);
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_tax_deductible'] = $request->boolean('is_tax_deductible', false);
        $data['is_default'] = $request->boolean('is_default', false);
        ExpenseCategory::create($data);
        return redirect()->route('expense_categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        $this->authorize('view', $expenseCategory);
        return view('expense_categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $this->authorize('update', $expenseCategory);
        return view('expense_categories.edit', compact('expenseCategory'));
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        $this->authorize('update', $expenseCategory);
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_tax_deductible'] = $request->boolean('is_tax_deductible', false);
        $data['is_default'] = $request->boolean('is_default', false);
        $expenseCategory->update($data);
        return redirect()->route('expense_categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $this->authorize('delete', $expenseCategory);
        $expenseCategory->delete();
        return redirect()->route('expense_categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}

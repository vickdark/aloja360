<?php

namespace App\Models;

use App\Enums\ExpenseCategory as ExpenseCategoryEnum;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'expense_category_id',
        'accommodation_id',
        'maintenance_request_id',
        'title',
        'description',
        'category',
        'amount',
        'tax_amount',
        'currency',
        'expense_date',
        'is_recurring',
        'recurrence_frequency',
        'payment_method',
        'supplier',
        'invoice_number',
        'receipt_path',
        'is_tax_deductible',
        'is_approved',
        'approved_by',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategoryEnum::class,
            'amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'expense_date' => 'date',
            'is_recurring' => 'boolean',
            'is_tax_deductible' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }
}

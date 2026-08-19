<?php

namespace Database\Seeders;

use App\Enums\ExpenseCategory as ExpenseCategoryEnum;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => ExpenseCategoryEnum::Utilities->value,
                'name' => ExpenseCategoryEnum::Utilities->label(),
                'icon' => 'fa-solid fa-bolt',
                'color' => '#f59e0b',
                'description' => 'Servicios públicos: agua, luz, gas, internet',
                'sort_order' => 1,
            ],
            [
                'slug' => ExpenseCategoryEnum::Maintenance->value,
                'name' => ExpenseCategoryEnum::Maintenance->label(),
                'icon' => 'fa-solid fa-wrench',
                'color' => '#ef4444',
                'description' => 'Reparaciones y mantenimiento preventivo o correctivo',
                'sort_order' => 2,
            ],
            [
                'slug' => ExpenseCategoryEnum::Payroll->value,
                'name' => ExpenseCategoryEnum::Payroll->label(),
                'icon' => 'fa-solid fa-users',
                'color' => '#3b82f6',
                'description' => 'Nómina, jornales y honorarios de personal',
                'sort_order' => 3,
            ],
            [
                'slug' => ExpenseCategoryEnum::Cleaning->value,
                'name' => ExpenseCategoryEnum::Cleaning->label(),
                'icon' => 'fa-solid fa-broom',
                'color' => '#10b981',
                'description' => 'Productos y servicios de limpieza y desinfección',
                'sort_order' => 4,
            ],
            [
                'slug' => ExpenseCategoryEnum::Supplies->value,
                'name' => ExpenseCategoryEnum::Supplies->label(),
                'icon' => 'fa-solid fa-boxes-stacked',
                'color' => '#8b5cf6',
                'description' => 'Suministros para huéspedes: papel, jabón, café, etc.',
                'sort_order' => 5,
            ],
            [
                'slug' => ExpenseCategoryEnum::Advertising->value,
                'name' => ExpenseCategoryEnum::Advertising->label(),
                'icon' => 'fa-solid fa-bullhorn',
                'color' => '#ec4899',
                'description' => 'Marketing, redes sociales y publicidad',
                'sort_order' => 6,
            ],
            [
                'slug' => ExpenseCategoryEnum::Transport->value,
                'name' => ExpenseCategoryEnum::Transport->label(),
                'icon' => 'fa-solid fa-car',
                'color' => '#64748b',
                'description' => 'Transporte, combustible y viáticos',
                'sort_order' => 7,
            ],
            [
                'slug' => ExpenseCategoryEnum::Taxes->value,
                'name' => ExpenseCategoryEnum::Taxes->label(),
                'icon' => 'fa-solid fa-receipt',
                'color' => '#dc2626',
                'description' => 'Impuestos nacionales, departamentales y municipales',
                'sort_order' => 8,
            ],
            [
                'slug' => ExpenseCategoryEnum::Commissions->value,
                'name' => ExpenseCategoryEnum::Commissions->label(),
                'icon' => 'fa-solid fa-percent',
                'color' => '#059669',
                'description' => 'Comisiones de plataformas OTA, pasarelas y ventas',
                'sort_order' => 9,
            ],
            [
                'slug' => ExpenseCategoryEnum::Other->value,
                'name' => ExpenseCategoryEnum::Other->label(),
                'icon' => 'fa-solid fa-ellipsis',
                'color' => '#9ca3af',
                'description' => 'Otros gastos operacionales diversos',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'description' => $category['description'],
                    'is_tax_deductible' => true,
                    'is_default' => true,
                    'sort_order' => $category['sort_order'],
                ]
            );
        }
    }
}

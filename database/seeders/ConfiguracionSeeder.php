<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // App settings
            [
                'key' => 'app_name',
                'value' => 'Aloja360',
                'group' => 'app',
            ],
            [
                'key' => 'app_subtitle',
                'value' => 'Gestión Integral de Alojamientos',
                'group' => 'app',
            ],
            [
                'key' => 'app_logo_icon',
                'value' => 'fa-solid fa-house-chimney',
                'group' => 'app',
            ],

            // Colores de la interfaz
            [
                'key' => 'color_primary',
                'value' => '#c05a1e',
                'group' => 'colores',
            ],
            [
                'key' => 'color_secondary',
                'value' => '#6c757d',
                'group' => 'colores',
            ],
            [
                'key' => 'color_sidebar_bg',
                'value' => '#1e1e2d',
                'group' => 'colores',
            ],
            [
                'key' => 'color_sidebar_text',
                'value' => '#ffffff',
                'group' => 'colores',
            ],

            // Empresa settings
            [
                'key' => 'empresa_nombre',
                'value' => 'Aloja360',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_id_fiscal',
                'value' => '',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_direccion',
                'value' => '',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_telefono',
                'value' => '',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_email',
                'value' => 'admin@aloja360.com',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_web',
                'value' => '',
                'group' => 'empresa',
            ],
        ];

        foreach ($settings as $setting) {
            Configuracion::updateOrCreate(['key' => $setting['key']], $setting);
        }

        Cache::forget('app_settings');
    }
}

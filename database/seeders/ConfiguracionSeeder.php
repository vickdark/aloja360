<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Configuracion;

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
                'value' => 'Laravel Boilerplate',
                'group' => 'app',
            ],
            [
                'key' => 'app_subtitle',
                'value' => 'Sistema en laravel',
                'group' => 'app',
            ],
            [
                'key' => 'app_logo_icon',
                'value' => 'fa-rocket',
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
                'value' => 'Mi Empresa S.A.',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_id_fiscal',
                'value' => '900.000.000-1',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_direccion',
                'value' => 'Calle 123 # 45 - 67, Ciudad',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_telefono',
                'value' => '+57 300 000 0000',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_email',
                'value' => 'contacto@miempresa.com',
                'group' => 'empresa',
            ],
            [
                'key' => 'empresa_web',
                'value' => 'www.miempresa.com',
                'group' => 'empresa',
            ],
        ];

        foreach ($settings as $setting) {
            Configuracion::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                "name" => "Usuarios",
                "slug" => "usuarios",
            ],
            [
                "name" => "Finanzas",
                "slug" => "finanzas",
                "subareas" => [
                    [
                        'name' => 'Financiera',
                        'slug' => 'financiera',
                    ],
                    [
                        'name' => 'Operativa',
                        'slug' => 'operativa',
                    ],
                    [
                        'name' => 'Ambiental',
                        'slug' => 'ambiental',
                    ],
                    [
                        'name' => 'RRHH',
                        'slug' => 'auditorias-rrhh',
                    ],
                    [
                        'name' => 'Cumplimiento',
                        'slug' => 'cumplimiento',
                    ],
                    [
                        'name' => 'Riesgos',
                        'slug' => 'riesgos',
                    ],
                    [
                        'name' => 'Seguridad',
                        'slug' => 'seguridad',
                    ],
                    [
                        'name' => 'Proyectos',
                        'slug' => 'auditorias-proyectos',
                    ],

                ],
            ],
            [
                "name" => "Cotizaciones",
                "slug" => "cotizaciones",
            ],
            [
                "name" => "Contactos",
                "slug" => "contactos",
            ],
            [
                "name" => "Donaciones",
                "slug" => "donaciones",
            ],
            [
                "name" => "Registro-Casos",
                "slug" => "registro-casos",
            ],
            [
                "name" => "Reportes",
                "slug" => "reportes",
            ],
            [
                "name" => "Aprobaciones",
                "slug" => "aprobaciones",
            ],
            [
                "name" => "Políticas",
                "slug" => "politicas",
            ],
            [
                "name" => "Certificados",
                "slug" => "certificados",
            ],
            [
                "name" => "Trámites y Licencias",
                "slug" => "licencias",
            ],
            [
                "name" => "Suscripciones",
                "slug" => "suscripciones",
            ],
            [
                "name" => "Recursos Humanos",
                "slug" => "rrhh"
            ],
            [
                "name" => "KPIS/Control calidad",
                "slug" => "kpis"
            ],
            [
                "name" => "Proyectos",
                "slug" => "proyectos"
            ],
            [
                "name" => "Obras",
                "slug" => "obras"
            ],
            [
                "name" => "Marketing",
                "slug" => "marketing"
            ],
            [
                "name" => "Cloud",
                "slug" => "cloud",
            ],
            [
                "name" => "Teams",
                "slug" => "teams",
            ],
            [
                "name" => "Reuniones",
                "slug" => "reuniones",
            ],
        ];

        foreach ($areas as $areaData) {
            $area = Area::firstOrCreate(
                ["slug" => $areaData["slug"]],
                ["name" => $areaData["name"]],
            );

            if (isset($areaData["subareas"])) {
                foreach ($areaData["subareas"] as $sub) {
                    Area::firstOrCreate(
                        ["slug" => $sub["slug"]],
                        [
                            "name" => $sub["name"],
                            "parent_id" => $area->id,
                        ],
                    );
                }
            }
        }
    }
}

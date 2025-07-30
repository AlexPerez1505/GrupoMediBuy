<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aparato;
use App\Models\ChecklistCategory;
use App\Models\ChecklistItem;

class ChecklistItemSeeder extends Seeder
{
    public function run()
    {
        $map = [
            'COLONOSCOPIO' => [
                'conexiones' => [
                    ['Conector de luz',        'Bueno y Funcional'],
                    ['Cubierta distal',        'Bueno y Funcional'],
                    ['Tubo de inserción',      'Funcional'],
                    ['Puerto de biopsia',      'Limpio y Funcional'],
                ],
                'botones' => [
                    ['Botón de succión',       'Funcional'],
                    ['Perrilla de control',    'Bueno y Fluida'],
                ],
                'componentes' => [
                    // añade si tienes más...
                ],
            ],
            'FUENTE DE LUZ L9000' => [
                'conexiones' => [
                    ['Cable de alimentación',  'Bueno'],
                ],
                'componentes' => [
                    ['Fibra de luz blanca',    'Intacta'],
                ],
            ],
            // … otros aparatos …
        ];

        foreach ($map as $aparatoName => $cats) {
            $aparato = Aparato::where('nombre', $aparatoName)->first();
            if (! $aparato) continue;

            foreach ($cats as $catName => $items) {
                $cat = ChecklistCategory::firstWhere('nombre', $catName);
                foreach ($items as [$nom, $res]) {
                    ChecklistItem::updateOrCreate(
                        [
                            'aparato_id'            => $aparato->id,
                            'checklist_category_id' => $cat->id,
                            'nombre'                => $nom,
                        ],
                        ['resultado' => $res]
                    );
                }
            }
        }
    }
}

<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChecklistCategory;

class ChecklistCategorySeeder extends Seeder
{
    public function run()
    {
        foreach (['conexiones','botones','componentes'] as $cat) {
            ChecklistCategory::firstOrCreate(['nombre' => $cat]);
        }
    }
}

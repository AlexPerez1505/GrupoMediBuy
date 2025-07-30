<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistCategory extends Model
{
    protected $table = 'checklist_categories';
    protected $fillable = ['nombre'];

    public function items()
    {
        return $this->hasMany(ChecklistItem::class, 'checklist_category_id');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $table = 'checklist_items';
    protected $fillable = [
        'aparato_id',
        'checklist_category_id',
        'nombre',
        'resultado',
    ];

    public function categoria()
    {
        return $this->belongsTo(ChecklistCategory::class, 'checklist_category_id');
    }

    public function aparato()
    {
        return $this->belongsTo(Aparato::class);
    }

    public function ordenes()
    {
        return $this->belongsToMany(
            Orden::class,
            'orden_checklist_item',
            'checklist_item_id',
            'orden_id'
        );
    }
}

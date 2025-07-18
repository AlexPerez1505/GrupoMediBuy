<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ✅ AÑADE ESTA LÍNEA
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory; // ✅ AGREGA HasApiTokens AQUÍ

    protected $fillable = [
        'nomina', 'password', 'email', 'name', 'imagen', 'phone',
        'cargo', 'puesto', 'vacaciones_disponibles', 'vacaciones_utilizadas',
        'permisos', 'retardos', 'role'
    ];

    // Función para verificar roles
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudMaterial::class);
    }
}

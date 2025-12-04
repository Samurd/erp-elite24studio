<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    // 1. ELIMINAMOS 'path'. Ya no guardamos la ruta de texto "cloud/root/..."
    protected $fillable = [
        "name",
        "parent_id",
        "user_id"
    ];

    /**
     * Ciclo de vida: BOOTED
     * Ya no necesitamos eventos complejos de recursividad física.
     * Si borras una carpeta, usamos Actions para limpiar la BD.
     */
    protected static function booted()
    {
        // Solo dejamos el borrado en cascada lógico si es necesario
        // Pero idealmente usas DeleteFolderAction.
    }

    /** * RELACIONES ESTRUCTURALES 
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, "parent_id");
    }

    public function children()
    {
        return $this->hasMany(Folder::class, "parent_id");
    }

    /**
     * RELACIÓN CON ARCHIVOS
     * Esta es una relación lógica (base de datos), no física.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * RELACIÓN CON DUEÑO
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * RELACIÓN CON SHARES
     * Para compartir carpetas enteras.
     */
    public function shares()
    {
        return $this->morphMany(Share::class, "shareable");
    }

    /**
     * Genera la ruta completa "Abuelo / Padre / Hijo" dinámicamente.
     * Uso: $folder->path_string
     */
    public function getPathStringAttribute()
    {
        // Empezamos con el nombre actual
        $path = collect([$this->name]);

        // Vamos subiendo por los padres
        $parent = $this->parent; // Esto usa la relación de arriba

        // Bucle para subir hasta la raíz (Cuidado: carga perezosa de queries)
        // Para optimizar en listas grandes, usa 'with('parent')' en tus controladores.
        while ($parent) {
            $path->prepend($parent->name); // Agrega al principio
            $parent = $parent->parent;
        }

        // Unimos con un separador bonito
        return $path->join(' / ');
    }
}
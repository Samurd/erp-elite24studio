# Sistema de Gestión de Archivos - Guía Completa

## 📋 Resumen Ejecutivo

Sistema centralizado para gestionar archivos vinculados a cualquier modelo mediante relaciones polimórficas. Permite subir archivos nuevos y vincular archivos existentes del Cloud.

**Implementación**: ~80 líneas en componente + ~230 líneas en vista + 3 líneas en modelo

---

## 🚀 Guía Rápida de Implementación

### Paso 1: Agregar Trait al Modelo (3 líneas)

```php
<?php
namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFiles;
    
    protected function getDefaultFolderName(): string
    {
        return 'payrolls'; // Carpeta por defecto
    }
}
```

### Paso 2: Actualizar Form (50 líneas)

**Agregar propiedades:**
```php
// Propiedades para manejo de archivos
#[Validate(['nullable', 'array'])]
#[Validate(['files.*' => 'file|max:2048'])]
public $files = [];

public $selected_folder_id = null;
public $linked_file_ids = [];
```

**Agregar método processFiles:**
```php
private function processFiles(Payroll $payroll)
{
    $fileManager = app(FileUploadManager::class);

    // Subir archivos nuevos
    if (!empty($this->files)) {
        foreach ($this->files as $file) {
            if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                $fileManager->uploadFile(
                    $file,
                    $payroll,
                    $this->selected_folder_id,
                    'payrolls'
                );
            }
        }
    }

    // Vincular archivos existentes
    if (!empty($this->linked_file_ids)) {
        foreach ($this->linked_file_ids as $fileId) {
            $fileManager->attachExistingFile($fileId, $payroll);
        }
    }
}
```

**Modificar store() y update():**
```php
public function store()
{
    $this->validate();
    
    $payroll = Payroll::create([...]);
    
    // Procesar archivos
    if (!empty($this->files) || !empty($this->linked_file_ids)) {
        $this->processFiles($payroll);
    }
}

public function update()
{
    $this->validate();
    
    $this->payroll->update([...]);
    
    // Procesar archivos
    if (!empty($this->files) || !empty($this->linked_file_ids)) {
        $this->processFiles($this->payroll);
    }
}
```

### Paso 3: Actualizar Componentes Create/Update (70 líneas)

**Agregar trait y propiedades (en ambos componentes):**
```php
use Livewire\WithFileUploads;

class Create extends Component // y Update extends Component
{
    use WithFileUploads;
    
    public Form $form;
    
    // Propiedades para archivos
    public $tempFiles = [];
    public $tempFile; // Para subida individual
    public $iteration = 1; // Para resetear input
    public $linkedFileIds = [];
    
    protected $listeners = [
        'file-selected' => 'handleFileSelected',
    ];
```

**Agregar métodos de gestión de archivos:**
```php
public function updatedTempFile()
{
    $this->validate([
        'tempFile' => 'file|max:10240', // 10MB
    ]);

    $this->tempFiles[] = $this->tempFile;
    $this->tempFile = null;
    $this->iteration++;
}

public function removeTempFile($index)
{
    if (isset($this->tempFiles[$index])) {
        unset($this->tempFiles[$index]);
        $this->tempFiles = array_values($this->tempFiles);
    }
}

public function openFileSelector()
{
    $this->dispatch('open-folder-selector', ['mode' => 'file']);
}

public function handleFileSelected($data)
{
    if (!in_array($data['id'], $this->linkedFileIds)) {
        $this->linkedFileIds[] = $data['id'];
        $this->dispatch('notify', 
            type: 'success',
            message: "Archivo '{$data['name']}' vinculado"
        );
    }
}

public function removeLinkedFile($fileId)
{
    $this->linkedFileIds = array_values(
        array_filter($this->linkedFileIds, fn($id) => $id != $fileId)
    );
}
```

**Modificar save():**
```php
public function save()
{
    // Pasar archivos al form
    $this->form->files = $this->tempFiles;
    $this->form->linked_file_ids = $this->linkedFileIds;
    
    $this->form->store(); // o update()
    
    session()->flash('success', 'Guardado exitosamente.');
    return redirect()->route('...');
}
```

**Agregar al render():**
```php
return view('...', [
    // ... otros datos
    'isEdit' => false, // o true en Update
]);
```

### Paso 4: Actualizar Vista (UI Premium)
 
Para mantener la consistencia visual y ofrecer una experiencia premium, utilizar la siguiente estructura en las vistas Blade.
 
#### 1. Vista Create/Update (`create.blade.php`)
 
Esta vista incluye la gestión completa: subir nuevos archivos y vincular existentes.
 
**Componentes necesarios al final del archivo:**
```blade
{{-- Modal Selector de Carpetas --}}
<livewire:components.folder-selector-modal />
```
 
**Estructura del Bloque de Archivos:**
```blade
<div class="mt-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <x-fas-paperclip class="w-5 h-5 text-gray-500" />
        Archivos Adjuntos
    </h3>
 
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            {{-- Acciones y Subida --}}
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                <div class="flex gap-2 items-center">


                    <button type="button" class="btn btn-sm btn-outline gap-2 normal-case font-medium" wire:click="openFileSelector">
                        <x-fas-link class="w-4 h-4" />
                        Vincular Existente
                    </button>
                </div>
                
                <div class="form-control w-full md:w-auto">
                    <label class="btn btn-sm btn-primary gap-2 normal-case font-medium cursor-pointer">
                        <x-fas-upload class="w-4 h-4" />
                        Subir Archivo
                        <input type="file" wire:model="tempFile" class="hidden" id="upload-{{ $iteration }}" />
                    </label>
                    <div wire:loading wire:target="tempFile" class="text-xs text-info mt-1 text-center">Subiendo...</div>
                </div>
            </div>
            @error('tempFile') <span class="text-error text-sm block mb-4">{{ $message }}</span> @enderror
 
            {{-- Lista de Archivos --}}
            @if(!empty($tempFiles) || !empty($linkedFileIds))
                <div class="grid grid-cols-1 gap-3">
                    {{-- Archivos Temporales --}}
                    @foreach($tempFiles as $index => $file)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 group hover:border-gray-300 transition-all">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm text-gray-400">
                                    <x-fas-file class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $file->getClientOriginalName() }}</p>
                                    <span class="badge badge-xs badge-warning">Nuevo</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-ghost btn-xs text-gray-400 hover:text-red-500" wire:click="removeTempFile({{ $index }})">
                                <x-fas-times class="w-4 h-4" />
                            </button>
                        </div>
                    @endforeach
 
                    {{-- Archivos Vinculados --}}
                    @foreach($linkedFileIds as $fileId)
                        @php $file = \App\Models\File::find($fileId); @endphp
                        @if($file)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 group hover:border-primary/30 hover:shadow-sm transition-all">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                                        <x-fas-file-alt class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $file->name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span>{{ $file->mime_type }}</span>
                                            <span>&bull;</span>
                                            <span class="badge badge-xs badge-info badge-outline">Vinculado</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-ghost btn-xs text-gray-400 hover:text-red-500" wire:click="removeLinkedFile({{ $fileId }})">
                                    <x-fas-trash class="w-4 h-4" />
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                        <x-fas-folder-open class="w-6 h-6" />
                    </div>
                    <p class="text-gray-500 text-sm">No hay archivos adjuntos</p>
                    <p class="text-gray-400 text-xs mt-1">Sube un archivo o selecciona uno existente</p>
                </div>
            @endif
        </div>
    </div>
</div>
```
 
#### 2. Vista Show (`show.blade.php`)
 
Vista de solo lectura con diseño de tarjetas grid.
 
```blade
@if($model->files->count() > 0)
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-fas-paperclip class="w-5 h-5 text-gray-500" />
            Archivos Adjuntos
        </h3>
 
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($model->files as $file)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between group hover:border-primary/30 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                            <x-fas-file-alt class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-700 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($file->size / 1024, 2) }} KB</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ Storage::url($file->path) }}" target="_blank" class="btn btn-ghost btn-xs text-gray-400 hover:text-primary" title="Ver">
                            <x-fas-eye class="w-4 h-4" />
                        </a>
                        <a href="{{ Storage::url($file->path) }}" target="_blank" class="btn btn-ghost btn-xs text-gray-400 hover:text-info" download title="Descargar">
                            <x-fas-download class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
```

---

---

## 📁 Arquitectura del Sistema

### Componentes Principales

1. **`HasFiles` Trait** (`app/Traits/HasFiles.php`)
   - Relación polimórfica `files()`
   - Métodos helper: `hasFiles()`, `filesCount()`, `getFilesWithRelations()`
   - Scope: `withFiles()`

2. **`FileUploadManager` Service** (`app/Services/FileUploadManager.php`)
   - `uploadFile()` - Sube archivo y lo vincula
   - `attachExistingFile()` - Vincula archivo existente
   - `deleteFile()` - Elimina archivo
   - `moveFile()` - Mueve archivo a otra carpeta
   - `getOrCreateDefaultFolder()` - Obtiene/crea carpeta por defecto

3. **`FolderSelectorModal` Component** (`app/Livewire/Components/FolderSelectorModal.php`)
   - Modal reutilizable para seleccionar carpetas/archivos
   - Modos: `folder` (seleccionar carpeta) o `file` (seleccionar archivo)
   - Eventos: `folder-selected`, `file-selected`

---

## 🎯 Funcionalidades

### ✅ Subir Archivos Nuevos
- Drag & drop o click para seleccionar
- **Un archivo a la vez** (Livewire no soporta `multiple` bien)
- Carpeta por defecto automática
- Opción de carpeta específica

### ✅ Vincular Archivos Existentes
- Buscar en todo el Cloud
- No duplica archivos
- Mantiene ubicación original
- Muestra ruta del archivo

### ✅ Visualizar Archivos Guardados
- Solo en modo edición
- Enlaces para descargar
- Información de carpeta y usuario

---

## 🔧 API del Trait HasFiles

```php
// Relación
$model->files; // Collection de archivos

// Métodos
$model->hasFiles(); // bool
$model->filesCount(); // int
$model->getFilesWithRelations(); // Collection con folder y user cargados
$model->filesInFolder($folderId); // Collection

// Scope
Model::withFiles()->get(); // Eager load de archivos
```

---

## 🔧 API del FileUploadManager

```php
$fileManager = app(FileUploadManager::class);

// Subir archivo nuevo
$file = $fileManager->uploadFile(
    $uploadedFile,      // UploadedFile
    $model,             // Modelo
    $folderId,          // ID carpeta específica (opcional)
    $defaultFolderName  // Nombre carpeta por defecto (opcional)
);

// Vincular archivo existente
$success = $fileManager->attachExistingFile($fileId, $model);

// Eliminar archivo
$success = $fileManager->deleteFile($fileId, $model);

// Mover archivo
$success = $fileManager->moveFile($fileId, $newFolderId);

// Obtener/crear carpeta por defecto
$folder = $fileManager->getOrCreateDefaultFolder('payrolls');
```

---

## 📝 Notas Importantes

### Carpeta Por Defecto
- Se define en `getDefaultFolderName()` del modelo
- Se crea automáticamente si no existe
- Ubicación: `cloud/root/{nombre-carpeta}`
- **Nunca cambia** para un modelo

### Carpeta Específica
- Opcional, permite elegir del Cloud
- Solo para archivos **nuevos**
- No afecta archivos existentes
- Útil para organizar por proyecto/cliente

### Archivos Vinculados
- No se duplican
- Mantienen ubicación original
- Solo se crea relación polimórfica
- Pueden vincularse a múltiples modelos

### Limitaciones de Livewire
- ❌ NO usar `multiple` en input file
- ✅ Subir archivos uno por uno
- ✅ Agregar múltiples archivos secuencialmente

---

## ⚠️ Problemas Conocidos y Limitaciones

### Limitación de S3 con Livewire
El driver de carga de archivos temporales de S3 en Livewire **NO soporta la carga múltiple simultánea**.
- **Síntoma**: Error `S3 temporary file upload driver only supports single file uploads`.
- **Solución**: Implementar la carga secuencial (uno por uno) como se describe en esta guía. **Nunca** usar el atributo `multiple` en el input file cuando se usa S3.

### Eliminación de Archivos Temporales
Al eliminar archivos del array `$tempFiles` en PHP, los índices numéricos pueden romperse si se usa `unset()`.
- **Síntoma**: Error `Call to a member function getClientOriginalName() on null` o comportamiento errático en la vista.
- **Solución**: Siempre reindexar el array usando `array_values()` después de eliminar un elemento.

---

## 🚨 Troubleshooting

### Error: "S3 temporary file upload driver only supports single file uploads"
**Causa**: El driver de S3 de Livewire no soporta `multiple` en el input file.
**Solución**:
1.  Cambiar el input a `wire:model="tempFile"` (singular) y quitar `multiple`.
2.  Implementar la lógica de carga secuencial con `updatedTempFile()`.

### Error: "Call to a member function getClientOriginalName() on null"
**Causa**: Al eliminar un archivo temporal del array `$tempFiles` usando `unset`, los índices no se reordenan, o el objeto archivo se pierde.
**Solución**:
1.  Usar el método `removeTempFile($index)` que hace `array_values()` para reindexar.
2.  Asegurarse de que la vista itera sobre `$tempFiles` correctamente.

### Error: "Cannot handle file upload without WithFileUploads trait"
**Solución**: Agregar `use WithFileUploads;` en el componente Create/Update.

### Error: "isNotEmpty() on array"
**Solución**: Ya corregido en `folder-selector-modal.blade.php` (usar `!empty()`).

### Modal se abre dos veces
**Solución**: Ya corregido - click en carpeta navega, botón "Seleccionar" selecciona.

### Archivos no se guardan
**Verificar**:
1. Trait `HasFiles` en el modelo.
2. `WithFileUploads` en el componente.
3. Archivos se pasan al Form antes de `store()`/`update()`: `$this->form->files = $this->tempFiles;`.
4. Form llama a `processFiles()`.

---

## ✨ Ventajas del Sistema

✅ **Centralizado**: Lógica en un solo lugar (`FileUploadManager`)  
✅ **Reutilizable**: Mismo patrón para todos los módulos  
✅ **Flexible**: Carpeta por defecto + carpeta específica opcional  
✅ **Eficiente**: No duplica archivos al vincular existentes  
✅ **Consistente**: UI idéntica en todos los módulos  
✅ **Mantenible**: Cambios en un solo lugar  

---

## 📚 Próximos Módulos

Para agregar archivos a un nuevo módulo, sigue los 4 pasos de la Guía Rápida.

**Tiempo estimado**: 30-45 minutos por módulo

**Módulos sugeridos**:
- Invoices
- Contracts
- Tax Records
- Gross Income
- Expenses

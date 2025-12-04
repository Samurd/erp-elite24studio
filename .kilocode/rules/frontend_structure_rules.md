# frontend_structure_rules.md

Estas reglas son para seguir una estructura y como contruir las paginas y rutas nuevas

Principlamente se usara Livewire para paginas y componentes frontend

## Guidelines

- Si va crear un nueva pagina o componente usar los comando oficiales, por ejemplo: `php artisan livewire:`
- Si es una pagina general, por ejemplo "Pagina de empleados", que seria para listar empleados, se crearia en el  file de rrhh que seria donde estair el modulo de rrhh '/routes/modules/rrhh.php' y ahi con todas sus respectivas rutas, mas detalles de un empleado '/employees/:id' y demas.

- Cuando es un ruta general, siempre preguntar si es modulo nuevo o no, si es nuevo modulo, se crea las rutas en nuevo file en routes, '/routes/modules/rrhh.php', si no es nuevo modulo, preguntar de que modulo es o donde seria esa nueva ruta o pagina, etc.

- Para las fechas usa `Carbon \Carbon\Carbon::parse();` o en el modelo usa $casts, por ejemplo: `$casts = ['start_date' => 'date', 'end_date' => 'date'];`
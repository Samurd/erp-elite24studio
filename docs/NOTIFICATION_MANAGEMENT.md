# Sistema de Notificaciones - Guía de Implementación

## Visión General

El sistema de notificaciones permite gestionar alertas automáticas y manuales para cualquier modelo del sistema. Soporta notificaciones inmediatas, programadas, recurrentes y recordatorios basados en fechas.

---

## 1. Integración en Modelos

Para agregar soporte de notificaciones a un modelo, sigue estos pasos:

### Paso 1: Agregar el Trait

```php
use App\Traits\HasNotifications;

class TuModelo extends Model
{
    use HasNotifications;
}
```

### Paso 2: Implementar Métodos de Configuración (Opcional)

El trait `HasNotifications` proporciona métodos por defecto, pero puedes sobrescribirlos en tu modelo para personalizar el comportamiento:

```php
// Título por defecto para las notificaciones
public function getDefaultNotificationTitle(): string
{
    return "Notificación de {$this->name}";
}

// Mensaje por defecto para recordatorios (reminder)
public function getDefaultNotificationMessage(Carbon $eventDate): string
{
    return "El evento {$this->name} vence el {$eventDate->format('d/m/Y')}.";
}

// Mensaje por defecto para recurrentes (recurring)
public function getDefaultRecurringMessage(): string
{
    return "Es hora de renovar {$this->name}.";
}

// Fecha base para recordatorios (si no usas campos estándar como 'renewal_date', 'due_date', etc.)
public function getEventDate(): ?Carbon
{
    return $this->mi_fecha_personalizada;
}

// Días por defecto para recordatorios
public function getRenewalReminderDays(): ?int
{
    return 3; // 3 días antes
}
```

---

## 2. Componente UI (`NotificationManager`)

El componente Livewire `NotificationManager` proporciona una interfaz completa para gestionar las notificaciones de un modelo.

### Uso Básico

En tu vista Blade (por ejemplo, en un modal de edición o una página de detalle):

```blade
<livewire:components.notification-manager :notifiable="$model" />
```

### Configuración Avanzada

Puedes restringir los tipos de notificaciones permitidos usando el parámetro `allowedTypes`:

```blade
<livewire:components.notification-manager 
    :notifiable="$model" 
    :allowedTypes="['now', 'scheduled']" 
/>
```

Tipos disponibles:
- `'now'`: Envío inmediato.
- `'scheduled'`: Programado para una fecha/hora específica.
- `'recurring'`: Se repite periódicamente.
- `'reminder'`: Basado en una fecha del modelo (X días antes).

---

## 3. Tipos de Notificaciones

### ⚡ Inmediata (`now`)
- **Comportamiento**: Se envía instantáneamente. No crea un template en la base de datos.
- **Uso**: Avisos urgentes, confirmaciones manuales.
- **Campos**: Título y Mensaje (opcionales, usan default del modelo si están vacíos).

### 🕒 Programada (`scheduled`)
- **Comportamiento**: Se envía una sola vez en la fecha y hora especificada.
- **Ciclo de Vida**: 
    1. Se crea como `active`.
    2. El sistema la envía cuando llega la fecha.
    3. Se marca automáticamente como `inactive` (no se vuelve a enviar).
    4. Desaparece de la lista de notificaciones activas (solo visible si se reactiva manualmente o por base de datos).
- **Validación**: Permite programar para el día actual (incluso si la hora ya pasó ligeramente, se enviará de inmediato).

### 🔄 Recurrente (`recurring`)
- **Comportamiento**: Se envía repetidamente según un intervalo (diario, semanal, mensual, anual).
- **Ciclo de Vida**:
    1. Se crea como `active`.
    2. Se envía en la fecha programada.
    3. Se recalcula la `next_send_at` según el intervalo.
    4. Permanece `active` indefinidamente.
- **Gestión**: Es el único tipo que muestra el botón de **Pausar/Reanudar** en la UI.

### 📅 Recordatorio (`reminder`)
- **Comportamiento**: Se envía X días antes de una fecha específica del modelo (ej. fecha de vencimiento).
- **Ciclo de Vida**: Similar a `scheduled`, se envía una vez y luego se desactiva automáticamente.
- **Requisito**: El modelo debe tener una fecha válida (detectada automáticamente o vía `getEventDate()`).

---

## 4. Personalización de Títulos y Mensajes

En la UI, los campos **Título** y **Mensaje** son opcionales.

- **Si se dejan vacíos**: El sistema usará automáticamente los métodos `getDefault...` definidos en el modelo (ver sección 1).
- **Si se completan**: Se usará el texto ingresado por el usuario.

Esto permite una experiencia rápida ("Enviar ahora" sin escribir nada) manteniendo la flexibilidad de personalizar el mensaje si es necesario.

---

## 5. Arquitectura y Servicios

### `NotificationService`
Es el núcleo del sistema. Maneja la creación de templates y el envío de notificaciones.

- `createImmediate(...)`: Envía directamente.
- `createScheduledTemplate(...)`: Crea template tipo `scheduled`.
- `createRecurringTemplate(...)`: Crea template tipo `recurring`.
- `createReminderTemplate(...)`: Crea template tipo `reminder`.

### Comandos Automáticos (Cron)
El sistema depende de comandos programados para procesar las notificaciones:

- `notifications:send-scheduled`: Procesa `scheduled`.
- `notifications:send-recurring`: Procesa `recurring`.
- `notifications:send-reminders`: Procesa `reminder`.

Estos deben estar configurados en el `Kernel` o `routes/console.php` para ejecutarse cada minuto o diariamente según corresponda.

---

## 6. Soporte de Email

Todas las notificaciones soportan envío por email opcional.

- **Email del Usuario**: Por defecto, se envía al email del usuario dueño del modelo (`$model->user->email`).
- **Email Personalizado**: En la UI, se puede activar "Enviar también por email" y especificar una dirección diferente (ej. para notificar a un cliente externo).

---

## Resumen de Cambios Recientes

- **Reconstrucción de UI**: Interfaz más limpia y reactiva.
- **Lógica de "Un solo uso"**: `scheduled` y `reminder` se desactivan tras el envío.
- **Validación Flexible**: Se permite programar para "hoy" sin errores de validación estricta.
- **Defaults Inteligentes**: Título y mensaje opcionales, delegando al modelo.

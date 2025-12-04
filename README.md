# ERP Elite 24 Studio

Sistema ERP desarrollado con Laravel 11, diseñado para ser escalable, robusto y fácil de desplegar.

## 🛠 Tecnologías Utilizadas

Este proyecto utiliza un stack moderno basado en PHP y JavaScript:

- **Backend:** Laravel 11
- **Frontend:** Livewire 3 + Alpine.js + TailwindCSS
- **Base de Datos:** MySQL 8.0
- **Cache & Colas:** Redis
- **WebSockets (Real-time):** Laravel Reverb
- **Infraestructura:** Docker & Docker Compose

## 🚀 Entorno de Desarrollo

Para trabajar en el proyecto localmente, utilizamos Docker para garantizar que todos los desarrolladores tengan el mismo entorno.

### Prerrequisitos
- Docker Desktop o Docker Engine
- Docker Compose

### Iniciar Entorno de Desarrollo

1. **Clonar el repositorio**
2. **Configurar variables de entorno:**
   ```bash
   cp .env.example .env
   ```
3. **Iniciar contenedores:**
   ```bash
   docker-compose up -d
   ```
   Esto iniciará la aplicación en modo desarrollo (con hot-reload para Vite si se ejecuta `npm run dev` localmente o en el contenedor).

4. **Acceso:**
   - App: http://localhost:8000
   - Base de datos: Puerto 3306
   - Redis: Puerto 6379

---

## 🧪 Pruebas de Producción en Local

Es crucial probar la construcción de producción localmente antes de desplegar, especialmente para verificar la compilación de assets (Vite) y la conexión de WebSockets (Reverb).

Para esto, hemos preparado una configuración específica que simula el entorno de producción (Dokploy) pero adaptado para correr en tu máquina.

### Pasos para probar el build de producción:

1. **Crear archivo de entorno para pruebas:**
   Copia el ejemplo de producción. Este archivo ya tiene configurado `REVERB_HOST=reverb` (interno) y `VITE_REVERB_HOST=localhost` (externo) para que funcione en tu PC.
   ```bash
   cp .env.example.prod .env.prod.test
   ```

2. **Ejecutar con Docker Compose de Test:**
   Utilizamos `docker-compose.prod.test.yml` que expone el puerto 8080 (Reverb) y pasa las variables de entorno necesarias durante el build.

   ```bash
   docker compose -f docker-compose.prod.test.yml --env-file .env.prod.test up -d --build
   ```

   > **Nota:** El flag `--build` es importante para asegurar que los assets de frontend se recompilen con las nuevas variables de entorno.

3. **Verificar la aplicación:**
   - **Web:** http://localhost (Puerto 80 por defecto)
   - **Reverb (WebSocket):** http://localhost:8080
   - **Test de WebSocket:** Puedes usar la herramienta de diagnóstico en http://localhost/test-websocket.html (si la has copiado al contenedor).

### ¿Por qué esta configuración especial?

En producción real (Dokploy/Traefik), los puertos se manejan internamente y se exponen vía dominios. En local, necesitamos:
1. **Mapeo de puertos explícito:** `8080:8080` para que tu navegador pueda conectar al WebSocket.
2. **Configuración Dual de Host:**
   - `REVERB_HOST=reverb`: Para que Laravel (backend) pueda hablar con el servidor WebSocket internamente dentro de la red de Docker.
   - `VITE_REVERB_HOST=localhost`: Para que tu navegador (frontend) sepa a dónde conectar el WebSocket desde fuera.

---

## 📦 Despliegue en Producción (Dokploy)

Para el despliegue real, se utiliza `docker-compose.prod.yml` junto con las variables de entorno configuradas en el panel de Dokploy.

La configuración principal reside en que:
- `REVERB_HOST` debe ser el nombre del servicio (`reverb`).
- `VITE_REVERB_HOST` debe ser el dominio público (ej. `ws.tudominio.com`).
- `APP_URL` debe ser el dominio de la aplicación (ej. `https://erp.tudominio.com`).

# API Spotify

API del proyecto Spotify desarrollada con Laravel, PostgreSQL, Redis, MongoDB, Docker y Laravel Sanctum.

Esta API actualmente permite:

- Registrar usuarios.
- Iniciar sesión.
- Generar tokens de autenticación automáticamente.
- Probar el registro y login desde Postman.

---

## 1. Requisitos

Antes de comenzar, tener instalado:

- Git
- Docker Desktop
- Node.js y npm
- Postman

Comprobar Git:

```powershell
git --version
```

Comprobar Docker:

```powershell
docker --version
```

Comprobar npm:

```powershell
npm --version
```

---

## 2. Clonar el proyecto

Abrir PowerShell y ejecutar:

```powershell
git clone URL_DEL_REPOSITORIO
```

Entrar a la carpeta:

```powershell
cd api
```

Cambiar a la rama de autenticación:

```powershell
git checkout feature/auth-api
```

---

## 3. Crear el archivo `.env`

Copiar el archivo de ejemplo:

```powershell
Copy-Item .env.example .env
```

No utilizar ni compartir el `.env` de otra computadora.

Cada integrante debe tener su propio `.env`.

---

## 4. Levantar Docker

Desde la carpeta `api` ejecutar:

```powershell
docker compose up -d
```

Comprobar que los contenedores estén funcionando:

```powershell
docker compose ps
```

Los servicios principales deben aparecer como `Up` o `running`.

La API estará disponible en:

```text
http://localhost:8000
```

---

## 5. Instalar dependencias

Para instalar las dependencias PHP dentro del contenedor de Docker:

```powershell
docker compose exec app composer install
```

*(Si trabajas de forma local sin usar el contenedor de Docker, puedes ejecutar directamente: `composer install`)*

Para instalar las dependencias de Node.js (frontend/assets):

```powershell
npm install
```

---

## 6. Generar la APP_KEY

Si el `.env` tiene `APP_KEY` vacío, ejecutar:

```powershell
docker compose exec app php artisan key:generate
```

---

## 7. Ejecutar las migraciones

Ejecutar:

```powershell
docker compose exec app php artisan migrate
```

Esto crea las tablas necesarias en la base de datos.

---

## 8. Limpiar la configuración de Laravel

Ejecutar:

```powershell
docker compose exec app php artisan optimize:clear
```

---

## 9. Probar el registro en Postman

Abrir Postman y crear una petición nueva.

### Método

```text
POST
```

### URL

```text
http://localhost:8000/api/register
```

### Body

Seleccionar:

```text
Body → raw → JSON
```

Enviar:

```json
{
    "name": "Juanito",
    "email": "juanito@test.com",
    "password": "12345678"
}
```

Presionar **Send**.

Si funciona, la API debe responder con:

```text
201 Created
```

y devolver un usuario y un token.

Ejemplo:

```json
{
    "message": "Usuario registrado correctamente.",
    "user": {
        "id": "...",
        "name": "Juanito",
        "email": "juanito@test.com"
    },
    "token": "..."
}
```

El `id` y el `token` serán diferentes para cada usuario.

---

## 10. Probar el Login en Postman

Crear otra petición.

### Método

```text
POST
```

### URL

```text
http://localhost:8000/api/login
```

### Body

Seleccionar:

```text
Body → raw → JSON
```

Enviar las mismas credenciales utilizadas durante el registro:

```json
{
    "email": "juanito@test.com",
    "password": "12345678"
}
```

Presionar **Send**.

Si funciona, debe devolver una respuesta exitosa junto con un token.

Ejemplo:

```json
{
    "message": "Login exitoso.",
    "user": {
        "id": "...",
        "name": "Juanito",
        "email": "juanito@test.com"
    },
    "token": "..."
}
```

---

## 11. Problemas comunes

### Docker no está iniciado

Abrir Docker Desktop y volver a ejecutar:

```powershell
docker compose up -d
```

### Ver los contenedores

```powershell
docker compose ps
```

### Ver los logs de la aplicación

```powershell
docker compose logs app
```

### Limpiar caché de Laravel

```powershell
docker compose exec app php artisan optimize:clear
```

### Ver las rutas de la API

```powershell
docker compose exec app php artisan route:list --path=api
```

Deben aparecer:

```text
POST  api/login
POST  api/register
```

---

## 12. Importante

No subir ni compartir el archivo:

```text
.env
```

El archivo que sí debe estar en GitLab es:

```text
.env.example
```

Cada integrante debe crear su propio `.env` usando:

```powershell
Copy-Item .env.example .env
```

---

# Resumen rápido

Después de clonar el proyecto:

```powershell
cd api
git checkout feature/auth-api
Copy-Item .env.example .env
docker compose up -d
docker compose exec app composer install
npm install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan optimize:clear
```

Después abrir Postman y probar:

```text
POST http://localhost:8000/api/register
```

y:

```text
POST http://localhost:8000/api/login
```

Con esto se puede comprobar que la autenticación de la API funciona correctamente.
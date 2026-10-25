# API Spotify

Backend del proyecto Spotify desarrollado con Laravel, PostgreSQL, Docker y Laravel Sanctum.

Esta API permite gestionar la autenticación de usuarios, perfiles, el catálogo musical (artistas, álbumes y canciones), la pantalla de inicio y el sistema de búsqueda.

## 1. Requisitos y preparación

Tener instalados Git, Docker Desktop y Postman.

Clona el repositorio y entra en la carpeta del proyecto:

```powershell
git clone URL_DEL_REPOSITORIO
cd api
Copy-Item .env.example .env
```

## 2. Levantar el backend con Docker

Ejecuta los siguientes comandos para iniciar los contenedores, instalar las dependencias, generar la llave de Laravel, ejecutar las migraciones y los seeders, y limpiar la caché:

```powershell
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan optimize:clear
```

> **Importante:** `migrate:fresh --seed` elimina las tablas existentes y las vuelve a crear. Úsalo solo si puedes reiniciar los datos de la base de datos.

La API estará disponible en:

```text
http://localhost:8000
```

---

## 3. Guía de pruebas en Postman

### A. Autenticación (rutas públicas)

#### 1. Registro

- **Método:** `POST`
- **URL:** `http://localhost:8000/api/register`
- **Body:** seleccionar `raw` y `JSON`.

```json
{
  "username": "Juanito",
  "email": "juanito@test.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### 2. Inicio de sesión

- **Método:** `POST`
- **URL:** `http://localhost:8000/api/login`
- **Body:** seleccionar `raw` y `JSON`.

```json
{
  "email": "juanito@test.com",
  "password": "password123"
}
```

**Importante:** copia el `token` que devuelve la petición de inicio de sesión. Para usarlo en las rutas protegidas:

1. Abre la pestaña **Authorization** de la petición en Postman.
2. En **Type**, selecciona `Bearer Token`.
3. Pega el token en el campo **Token**, sin escribir `Bearer` delante. Postman agregará automáticamente el encabezado `Authorization`.
4. En la pestaña **Headers**, agrega este encabezado:

| Key | Value |
|---|---|
| `Accept` | `application/json` |

### B. Gestión del perfil (requiere token)

| Método | URL | Descripción |
|---|---|---|
| `GET` | `http://localhost:8000/api/user` | Consultar tu perfil. |
| `PUT` | `http://localhost:8000/api/user` | Actualizar el perfil. Enviar un JSON con `username`, `email` o `avatar_url`. |
| `DELETE` | `http://localhost:8000/api/user` | Eliminar tu cuenta. |

### C. Catálogo musical (requiere token)

#### 1. Solicitar ser artista

- **Método:** `POST`
- **URL:** `http://localhost:8000/api/artists`

```json
{
  "stage_name": "Bad Bunny"
}
```

> **Nota:** La solicitud se crea automáticamente con estado `pending`. El usuario seguirá teniendo el rol de "Oyente" y no podrá subir álbumes ni canciones hasta que un Administrador apruebe su solicitud.

#### 2. Crear un álbum (Solo para artistas verificados)

- **Método:** `POST`
- **URL:** `http://localhost:8000/api/albums`

```json
{
  "artist_id": "TU_UUID_DE_ARTISTA",
  "genre_id": 4,
  "title": "Un Verano Sin Ti",
  "cover_url": "https://link.com/cover.jpg",
  "release_date": "2022-05-06",
  "album_type": "album"
}
```

Reemplaza `TU_UUID_DE_ARTISTA` por el UUID de tu artista. El token utilizado debe pertenecer a un usuario cuyo estado sea `verified` y tenga el rol de Artista.

#### 3. Crear y listar canciones

**Crear una canción**

- **Método:** `POST`
- **URL:** `http://localhost:8000/api/songs`

```json
{
  "album_id": "TU_UUID_DE_ALBUM",
  "title": "Me Porto Bonito",
  "duration_seconds": 178,
  "audio_url": "https://link.com/audio.mp3",
  "track_number": 3
}
```

Reemplaza `TU_UUID_DE_ALBUM` por el UUID de un álbum existente.

**Listar canciones**

- **Método:** `GET`
- **URL:** `http://localhost:8000/api/songs`

Devuelve la lista de canciones con la información relacionada del artista y el género, según la respuesta configurada en la API.

### D. Panel de Administrador (Flujo de Aprobación de Artistas)

Para probar estas rutas, necesitas un token de administrador. Inicia sesión (`POST /api/login`) con las credenciales creadas por el seeder:
- **Email:** `admin@test.com`
- **Password:** `Admin123$`

#### 1. Ver solicitudes de artistas pendientes
- **Método:** `GET`
- **URL:** `http://localhost:8000/api/admin/artist-requests`

Lista todos los usuarios que enviaron solicitud para ser artistas. Copia el `id` (UUID del modelo Artist) que aparece en la respuesta para usarlo en los siguientes endpoints.

#### 2. Aprobar solicitud
- **Método:** `PUT`
- **URL:** `http://localhost:8000/api/admin/artist-requests/TU_UUID_DE_ARTISTA/approve`

Cambia el estado de la solicitud a `verified` y actualiza el rol del usuario a "Artista", dándole permisos para subir música. (No requiere Body).

#### 3. Rechazar solicitud
- **Método:** `DELETE`
- **URL:** `http://localhost:8000/api/admin/artist-requests/TU_UUID_DE_ARTISTA/reject`

Elimina la solicitud de la base de datos, permitiendo que el usuario "Oyente" pueda enviar una nueva solicitud en el futuro. (No requiere Body).

### E. Pantalla de inicio y búsqueda

#### 1. Buscador

- **Método:** `GET`
- **URL:** `http://localhost:8000/api/search`

La búsqueda recibe el parámetro `query`. En Postman puedes enviarlo desde la pestaña **Params**:

| Key | Value |
|---|---|
| `query` | `Bad Bunny` |

Escribe el término que quieras buscar en **Value** y asegúrate de que la casilla del parámetro esté marcada. Después, presiona **Send**.

También puedes escribir el parámetro directamente en la URL. Por ejemplo:

```text
http://localhost:8000/api/search?query=Bad%20Bunny
```

**Importante:** el nombre del parámetro debe ser `query`, porque así lo recibe el controlador de búsqueda. No uses `q`. Actualmente, la búsqueda consulta títulos de canciones y nombres artísticos.

---

## 4. Problemas comunes

| Problema o tarea | Comando |
|---|---|
| Iniciar los contenedores | `docker compose up -d` |
| Ver los contenedores activos | `docker compose ps` |
| Consultar los logs del contenedor de la aplicación | `docker compose logs app` |
| Listar las rutas de la API | `docker compose exec app php artisan route:list --path=api` |
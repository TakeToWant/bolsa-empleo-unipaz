# 🚀 Deploy en Railway — Bolsa de Empleo UNIPAZ

## Requisitos previos

- Cuenta en [github.com](https://github.com) (gratis)
- Cuenta en [railway.app](https://railway.app) (gratis con GitHub)

---

## Paso 1 — Subir el proyecto a GitHub

Abre una terminal en la carpeta del proyecto y ejecuta:

```bash
git init
git add .
git commit -m "Bolsa de Empleo UNIPAZ - deploy inicial"
```

Luego ve a [github.com/new](https://github.com/new), crea un repositorio llamado `bolsa-empleo-unipaz` (privado) y ejecuta:

```bash
git remote add origin https://github.com/TU-USUARIO/bolsa-empleo-unipaz.git
git branch -M main
git push -u origin main
```

---

## Paso 2 — Crear proyecto en Railway

1. Ve a [railway.app](https://railway.app) e inicia sesión con GitHub
2. Clic en **"New Project"**
3. Selecciona **"Deploy from GitHub repo"**
4. Elige tu repositorio `bolsa-empleo-unipaz`
5. Railway detectará automáticamente que es PHP/Laravel

---

## Paso 3 — Configurar Variables de Entorno

En tu proyecto de Railway ve a **Variables** y agrega estas una por una:

| Variable               | Valor                                 |
| ---------------------- | ------------------------------------- |
| `APP_NAME`             | `Bolsa de Empleo UNIPAZ`              |
| `APP_ENV`              | `production`                          |
| `APP_DEBUG`            | `false`                               |
| `APP_KEY`              | _(Railway lo genera con el start.sh)_ |
| `DB_CONNECTION`        | `sqlite`                              |
| `SESSION_DRIVER`       | `file`                                |
| `QUEUE_CONNECTION`     | `database`                            |
| `GOOGLE_CLIENT_ID`     | _tu client id de Google_              |
| `GOOGLE_CLIENT_SECRET` | _tu client secret de Google_          |

> La variable `APP_URL` y `GOOGLE_REDIRECT_URI` las agregas **después** de obtener el dominio en el Paso 4.

---

## Paso 4 — Obtener el dominio

1. En Railway, ve a tu servicio → pestaña **Settings**
2. En la sección **Networking** → clic en **"Generate Domain"**
3. Obtendrás una URL como: `https://bolsa-empleo-unipaz-production.up.railway.app`
4. Vuelve a **Variables** y agrega:
    - `APP_URL` = `https://bolsa-empleo-unipaz-production.up.railway.app`
    - `GOOGLE_REDIRECT_URI` = `https://bolsa-empleo-unipaz-production.up.railway.app/auth/google/callback`

---

## Paso 5 — Actualizar Google OAuth

En [console.cloud.google.com](https://console.cloud.google.com):

1. Ve a tu proyecto → **APIs y servicios → Credenciales**
2. Edita tu cliente OAuth
3. En **"URIs de redireccionamiento autorizados"** agrega:
    ```
    https://bolsa-empleo-unipaz-production.up.railway.app/auth/google/callback
    ```

---

## Paso 6 — Verificar el deploy

Espera que Railway haga el build (2-3 minutos). Cuando diga **"Active"** entra a tu URL.

**Credenciales de acceso:**
| Rol | Correo | Contraseña |
|-----|--------|-----------|
| Administrador | admin@unipaz.edu.co | Admin2024* |
| Empresa | info@tecnosoluciones.com | Empresa2024* |

---

## Actualizar el proyecto

Cada vez que hagas cambios locales y quieras reflejarlos en producción:

```bash
git add .
git commit -m "descripción del cambio"
git push
```

Railway hará el redeploy automáticamente.

---

## Notas importantes

- **SQLite en Railway**: Los datos persisten mientras el contenedor esté activo. Para producción real, considera agregar un plugin de **MySQL** en Railway.
- **Almacenamiento de archivos**: Los CVs y logos subidos se guardan en el servidor. Para mayor durabilidad, considera configurar un bucket S3 o Cloudflare R2.
- **Cola de correos**: Inicia el worker con `php artisan queue:work` o configura un segundo servicio en Railway.

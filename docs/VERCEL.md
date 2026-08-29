# Despliegue completo en Vercel

La aplicación se despliega como un único contenedor. Laravel atiende la web y
la API, mientras Nginx sirve React + Phaser bajo `/play/`.

## Servicios externos necesarios

1. Un clúster de MongoDB accesible desde Internet, por ejemplo MongoDB Atlas.
2. Un bucket S3-compatible con una URL pública para avatares, banners,
   imágenes y vídeos. Sirven AWS S3, Cloudflare R2 o Backblaze B2.
3. Una cuenta de Stripe si se van a habilitar pagos reales.

Vercel no ejecutará el servicio `mongo` de `docker-compose.yml`; la base de
datos siempre debe ser externa. Los archivos tampoco se guardan dentro del
contenedor porque sus instancias son efímeras.

## Variables de entorno

Usa `.env.vercel.example` como lista de referencia y crea las variables desde
Project Settings > Environment Variables. Nunca subas el `.env` local.

Variables obligatorias:

- `APP_KEY`: genera una clave una sola vez con `php artisan key:generate --show`.
- `DB_DSN` y `DB_DATABASE`: conexión al clúster MongoDB.
- `STRIPE_KEY` y `STRIPE_SECRET`.
- `UPLOADS_DRIVER=s3`.
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET` y `AWS_URL`.
- `AWS_DEFAULT_REGION`; para R2 normalmente se usa `auto`.
- `AWS_ENDPOINT` cuando el proveedor no es AWS.

Para Cloudflare R2 configura además `UPLOADS_VISIBILITY=private`; la URL de un
bucket o dominio público indicada en `AWS_URL` seguirá siendo la utilizada para
mostrar los objetos. Para AWS S3 público puede mantenerse `public`.

`APP_URL`, `FRONTEND_URL`, `SANCTUM_STATEFUL_DOMAINS` y
`CORS_ALLOWED_ORIGINS` se detectan automáticamente en Vercel. Cuando añadas un
dominio propio, establece `APP_URL` y `FRONTEND_URL` a la misma URL HTTPS.

## Crear el proyecto

1. Sube el repositorio a GitHub, GitLab o Bitbucket.
2. En Vercel selecciona **Add New > Project** e importa el repositorio.
3. Deja **Root Directory** en la raíz del repositorio.
4. Selecciona el preset **Container** si no se detecta automáticamente.
5. Añade las variables anteriores para Production y Preview.
6. Pulsa **Deploy**. Vercel detectará `Dockerfile.vercel`.

También puedes desplegar desde la raíz con la CLI:

```bash
vercel
vercel --prod
```

## Verificación posterior

Comprueba, en este orden:

1. `/up` responde correctamente.
2. La portada y `/play/` cargan sus recursos.
3. Registro, inicio y cierre de sesión.
4. Subida de avatar y banner; recarga la página para confirmar persistencia.
5. Compra de prueba con Stripe y retorno a `/checkout/success`.
6. Guardado y consulta de puntuaciones.

Si se configura un dominio propio, actualiza también las URL de retorno o
webhooks que hayas definido dentro del panel de Stripe.

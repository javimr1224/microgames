# Agentes gratuitos para Issues de Microgames

## Activación inicial

1. Incorpora este PR en `main` tras revisarlo. GitHub solo dispara el evento `issues` si el workflow está en la rama predeterminada. El instalador no hace merge automáticamente.
2. En [Google AI Studio](https://aistudio.google.com/apikey), crea una clave Gemini API de un **proyecto en Free Tier, sin facturación de pago habilitada**. No uses una clave de un proyecto de pago. El modelo fijado es `gemini-2.5-flash`, con entrada y salida gratuitas en su Free Tier. No se usa Vertex AI, grounding, búsqueda, Batch ni servicios adicionales.
3. En GitHub → repositorio → **Settings → Secrets and variables → Actions → Secrets → New repository secret** crea exactamente:

   **Nombre:** `GEMINI_API_KEY`  
   **Valor:** la clave completa de Google AI Studio.

   No la pegues en un Issue, commit, chat, `.env` o variable pública. Este es el **único secreto que debes crear**. `GITHUB_TOKEN` lo proporciona GitHub automáticamente y no requiere PAT.
4. En **Settings → Actions → General → Workflow permissions**, habilita **Allow GitHub Actions to create and approve pull requests**. Los permisos concretos están limitados en el YAML; no se aprueban ni fusionan PRs automáticamente. Una política de organización puede bloquear esta opción.
5. Confirma que el repositorio sigue público y usa runners estándar `ubuntu-24.04`. Estos runners de Actions son gratuitos para repositorios públicos. No se usan runners grandes, servicios de pago, cachés ni almacenamiento de dependencias. El artefacto pequeño de propuesta se conserva un día y está sujeto a las cuotas de almacenamiento de tu cuenta.
6. En **Settings → Secrets and variables → Actions → Variables**, crea `AGENT_FREE_TIER_CONFIRMED` con valor `true`, **solo después de verificar los pasos anteriores**. Hasta entonces el workflow se detiene antes de llamar a Gemini. Esta confirmación no consulta ni cambia la facturación: la API de generación no permite demostrar que una clave pertenece al nivel gratuito.
7. Crea la etiqueta **`agent`** en Issues → Labels si aún no existe.

Las cuotas gratuitas son limitadas y pueden cambiar. Si se agotan, el sistema se detiene: no cambia de modelo/proveedor ni activa facturación. Para mantener 0 €, no habilites pago en el proyecto de Gemini. Esta configuración rechaza repositorios privados. No basta con que un modelo tenga Free Tier si tu clave está en un proyecto de pago.

## Uso desde Issues (también con el ordenador apagado)

1. Crea un Issue con un objetivo pequeño, archivos o pantallas implicados y criterios de aceptación comprobables.
2. Añade `agent` con una cuenta que tenga permiso **write, maintain o admin**. Empieza inmediatamente en GitHub; puedes hacerlo antes de dormir y apagar el ordenador. No hace falta un servidor propio. No hay un cron recurrente: una etiqueta añadida equivale a una ejecución.
3. Sigue **Actions → Microgames issue agent**. El agente lee título/cuerpo y `AGENTS.md` del repo, examina archivos, escribe código y recibe los resultados de validación para corregirse.
4. Al terminar con cambios crea `agent/issue-<n>-<slug>`, un commit y un **PR en borrador** contra `main`. Los resultados aparecen en el PR y en el resumen de Actions. Revisa el código, prueba el comportamiento y decide tú si hacer merge.
5. Si no hay cambios, verás la razón en el resumen de Actions y no se crea un PR vacío. Un fallo de preparación o falta de secreto deja una ejecución fallida. Una respuesta 429 o error del modelo detiene las llamadas; los cambios ya generados se validan y se entregan como borrador si existen.

Ejemplo de Issue:

> **Título:** Añadir botón para reiniciar Snake  
> **Objetivo:** En la pantalla de fin de partida, permitir iniciar una partida nueva sin recargar la página.  
> **Aceptación:** Reinicia puntuación y tablero; funciona con ratón y teclado; no cambia otros juegos; mantiene build y lint.

Editar un Issue ya etiquetado no lanza otra ejecución. Puedes quitar y volver a poner la etiqueta, o usar **Run workflow** en la rama predeterminada con `issue_number`. Debe seguir abierto y etiquetado. Si editas título/cuerpo o cambias etiquetas durante una ejecución, la publicación se rechaza para evitar resolver una solicitud distinta a la autorizada.

## Límites e idempotencia

- 40 minutos como máximo para el trabajo y 5 para publicación; preparación limitada a 15, generación/validación a 22.
- Hasta 24 peticiones a Gemini, separadas al menos por 8 segundos; cada petición tiene 60 segundos de límite. Presupuesto de generación de 15 minutos, reservando margen para validar.
- Máximo 3 rondas de validación (dos oportunidades de corrección), cada una limitada a 5 minutos; cada comando tiene límite de 3 minutos. Siempre se valida el último código, incluso si se agotan las llamadas.
- Hasta 20 archivos; 60 KB por archivo; 400 KB de contenido total. Sin borrado de archivos ni cambios de dependencias desde el agente.
- Una ejecución por Issue a la vez. Issues distintos pueden ejecutarse en paralelo y compartir cuota Gemini; empieza con una tarea pequeña antes de lanzar cinco.
- No hay reintentos automáticos de API ni de publicación. Una rama `agent/issue-N-*` existente evita otra ejecución; nunca se pisa ni se fuerza un push.
- Para repetir una tarea con rama existente, revisa/cierra su PR y elimina expresamente esa rama antes de relanzar. Si GitHub creó la rama pero rechazó la creación del PR, abre el PR manualmente desde esa rama; no pierdas el trabajo borrándola sin revisar.
- El agente entrega borradores aunque existan fallos de pruebas, indicando su estado. No afirma que un fallo sea previo: no ejecuta una comparación automática contra la base.

## Comprobaciones reales de Microgames

El contenedor instala las dependencias bloqueadas de Composer y de los dos proyectos npm, sin scripts de instalación ni plugins Composer. PHP 8.3, extensión MongoDB 2.3.0, Node 22 y MongoDB 7.0. La descarga de dependencias ocurre antes de introducir código generado y sin secretos. Después:

| Ubicación | Comprobación |
| --- | --- |
| Raíz | `php artisan package:discover --ansi` |
| Raíz | `npm run build` |
| Raíz | `npm run lint` si existe (actualmente no existe) |
| `frontend/` | `npm run build` |
| `frontend/` | `npm run lint` |
| Ambos proyectos npm | `npm run test -- --run` si existe (actualmente no existe) |
| Raíz | `php artisan test` |

Los comandos disponibles se intentan aunque falle uno anterior, salvo que se agote el tiempo total. Si en el futuro se añade un test runner npm que no acepte `--run`, un mantenedor debe adaptar `checks.mjs`. Cada ronda tiene un MongoDB nuevo, base `microgames_testing` y clave Laravel pública exclusiva de pruebas. No se crea ni modifica ningún `.env`, incluido `frontend/.env.local`, que se excluye de la copia de pruebas.

## Seguridad y alcance

- Solo un mantenedor con escritura puede dispararlo; se comprueba vía API antes de usar Gemini y antes de publicar. Usuarios externos pueden proponer Issues, pero no autorizar ejecución.
- Checkout sin credenciales persistentes. Título/cuerpo del Issue nunca se interpolan en comandos de shell.
- Gemini tiene herramientas de lectura/escritura limitadas y validación fija, sin terminal libre. Se rechazan rutas fuera del alcance, traversal, symlinks, `.env*`, claves comunes, instrucciones AGENTS y archivos de infraestructura.
- Solo edita `app/`, `routes/`, `resources/`, `frontend/src/`, `tests/`, `database/migrations/`, `database/factories/` y `docs/`, con extensiones de código/texto permitidas. Cambios en configuración, dependencias o despliegues requieren trabajo humano separado.
- La clave Gemini solo está en el controlador. Los procesos hijo reciben un entorno reducido y no heredan claves ni tokens. El código propuesto se ejecuta en Docker sin capacidades, con límites de recursos, montajes de solo lectura y una red interna exclusivamente para MongoDB. No recibe Docker socket, Git ni claves de producción.
- Publicación en un job nuevo: verifica de nuevo las rutas, identidad del Issue y SHA base, y usa la API Git para crear árbol, commit y referencia (equivalente a commit/push) sin ejecutar código propuesto. Nunca modifica la rama predeterminada.
- Los PRs siempre son borradores. La revisión humana sigue siendo necesaria: una prueba puede ser incompleta o estar modificada por el agente. No hay despliegue ni merge en estos workflows. Revisa también integraciones externas de previews que reaccionen a nuevas ramas/PRs: quedan fuera de este sistema.
- Solo se guarda un JSON pequeño con cambios y estados, sin dependencias, clave ni salida completa de pruebas. Los mensajes de las pruebas se usan como feedback en memoria y se envían a Gemini; no introduzcas datos privados en fixtures. El nivel gratuito de Gemini puede usar contenido para mejorar productos según sus condiciones.
- PRs creados con `GITHUB_TOKEN` normalmente no disparan nuevos workflows de push/PR. Las validaciones se ejecutan aquí, antes del PR. No añadas un PAT solo para eludir este comportamiento.

## Validar o desactivar la infraestructura

`node --test .github/agent/control.test.mjs` comprueba límites, rutas, finalización, reparación y cuotas con un modelo simulado, sin gastar API. `Validate agent infrastructure` ejecuta estas pruebas, actionlint y una comprobación del contenedor con los tests/build/lint reales al cambiar la infraestructura en un PR. El job `sandbox` puede fallar por errores de la aplicación; sus resultados se muestran por separado. La primera ejecución real con tu clave comprobará además el acceso a Gemini.

Para detener una ejecución, pulsa **Cancel workflow**. Para desactivar nuevas ejecuciones, cambia `AGENT_FREE_TIER_CONFIRMED` a `false`, elimina el secreto o deshabilita `Microgames issue agent` en Actions.

Fuentes oficiales: [Gemini: precios](https://ai.google.dev/gemini-api/docs/pricing), [límites](https://ai.google.dev/gemini-api/docs/rate-limits), [claves API](https://ai.google.dev/gemini-api/docs/api-key), [Actions: facturación](https://docs.github.com/en/actions/concepts/billing-and-usage), [secretos](https://docs.github.com/en/actions/how-tos/write-workflows/choose-what-workflows-do/use-secrets), [eventos Issues](https://docs.github.com/en/actions/reference/workflows-and-actions/events-that-trigger-workflows#issues).

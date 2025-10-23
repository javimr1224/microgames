# 🎮 Microgames

**Microgames** es una aplicación web que tiene el objetivo de ofrecer una experiencia de minijuegos en línea donde los usuarios pueden **registrarse, identificarse y desbloquear juegos de pago** desarrollados con **Phaser.js**.

El proyecto integra un **backend en Laravel**, un **frontend con React + Phaser**, y una **base de datos MongoDB** que gestiona usuarios, roles, permisos y juegos adquiridos.  
Todo el sistema está preparado para **desplegarse mediante Docker** y entornos virtualizados como **Proxmox** .

---

## 🚀 Stack Tecnológico

| Componente | Tecnología |
|-------------|-------------|
| **Frontend** | React + Phaser.js
| **Backend** | Laravel 12 (PHP 8.2) |
| **Base de datos** | MongoDB |
| **Autenticación y Roles** | Laravel Breeze + Middleware personalizado |
| **Pasarela de pago** | Stripe (modo test y producción) |
| **Despliegue** | Docker Compose / Proxmox |
| **Control de versiones** | Git + GitHub (repositorio público) |

---

## ⚙️ Instalación y Ejecución (Local)

### 1. Clonar el repositorio
```bash
git clone https://github.com/javimr1224/microgames.git
cd microgames
```

### 2. Crear archivo '.env' en la carpeta 'backend'
Configura tus variables de entorno:
```bash
APP_NAME=Microgames
APP_ENV=local
APP_KEY=base64:GENERAR_CON_PHP_ARTISAN_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mongodb
DB_HOST=mongodb
DB_PORT=27017
DB_DATABASE=microgames
DB_USERNAME=root
DB_PASSWORD=example

STRIPE_KEY= stripe_public_key
STRIPE_SECRET= stripe_secret_key
```

### 3. Construir con Docker
```bash
docker-compose up --build
```

Este comando levantará:
- `frontend` → React + Phaser 
- `backend` → Laravel + API REST  
- `mongodb` → Base de datos principal  
- `nginx` (opcional) → Servidor de producción

---

## 🌐 Despliegue

Microgames se podria desplegar con las siguientes plataformas:
- **Proxmox** (máquina virtual configurada desde el gestor)
- **Docker Hub**

Para entornos locales o presentaciones:
```bash
docker-compose up
```
El proyecto será accesible desde:
- Frontend → [http://localhost:3000](http://localhost:3000)  
- Backend API → [http://localhost:8000](http://localhost:8000)

---

## 🧩 Funcionalidades Implementadas / En Desarrollo

- Autenticación de usuarios (registro e inicio de sesión)
- Sistema de roles (admin / usuario)
- Gestión de juegos (listado, compra, desbloqueo)
- Integración con Stripe (modo test)
- Dashboard administrativo
- Minijuegos con Phaser.js
- API REST para comunicación con el frontend

---

## 📄 Documentación Adicional

- **Descripción y justificación del proyecto:**  
  [📘 Microgames_TFG_Documentacion.pdf](./Microgames_TFG_Documentacion.pdf)

- **Esquema de base de datos (MongoDB):**  
  Incluido en la La documentación

---

## 👨‍💻 Autor

**Javier Martínez Rodríguez**  
Grado Superior en Desarrollo de Aplicaciones Web (DAW)  
[GitHub](https://github.com/javimr1224) · [Email](mailto:tuemail@ejemplo.com)


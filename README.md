# EasyApp - Plataforma de Solicitudes de Asesoría

## 1. Visión General del Proyecto y Arquitectura

**EasyApp** es una aplicación web diseñada para gestionar solicitudes de asesoría. Originalmente un proyecto monolítico en PHP, ha sido refactorizado a una **arquitectura desacoplada y moderna** para mejorar su escalabilidad, mantenibilidad y la experiencia del desarrollador.

La nueva arquitectura se basa en la **separación de intereses (front-end/back-end)**:

-   **Back-end (API REST en PHP):**
    -   Construido en **PHP nativo (sin frameworks)**, funciona exclusivamente como una API REST.
    -   Toda la lógica de negocio (interacción con la base de datos, validaciones, autenticación) está encapsulada en la carpeta `businesslogic/`, siguiendo un patrón de **Servicios y Repositorios**.
    -   Los *endpoints* de la API se encuentran en la carpeta `api/` y gestionan las peticiones HTTP, devolviendo respuestas en formato **JSON**.
    -   Utiliza sesiones basadas en cookies para la autenticación de administradores.

-   **Front-end (Aplicación de una Sola Página en Angular):**
    -   Desarrollado con **Angular (standalone components)**, reside en la carpeta `frontend/`.
    -   Se comunica con el back-end de forma asíncrona a través de los *endpoints* de la API REST.
    -   Incluye un formulario de registro público y un panel de administración protegido para visualizar las solicitudes.
    -   La compilación de producción se genera en la carpeta `static/`, que es servida directamente por el servidor web.

## 2. Guía de Despliegue Local

Para configurar el proyecto en un entorno de desarrollo local, sigue estos pasos:

### Prerrequisitos

-   **PHP:** 8.0 o superior.
-   **Servidor Web:** Apache o Nginx con soporte para PHP.
-   **Base de Datos:** MySQL o MariaDB.
-   **Node.js:** Versión 20.x (LTS) o superior.
-   **Angular CLI:** `npm install -g @angular/cli`.

### Pasos de Configuración

1.  **Clonar el Repositorio:**
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd talentotech2_easyapp
    ```

2.  **Configurar la Base de Datos:**
    -   Crea una base de datos en tu servidor MySQL.
    -   Importa la estructura de las tablas desde un archivo SQL de volcado (si se proporciona en `documents/database.sql`).
    -   Configura las variables de entorno para la conexión a la base de datos. Puedes crear un archivo `.env` en la raíz del proyecto o configurar las variables directamente en tu servidor:
        ```env
        DB_HOST=localhost
        DB_NAME=nombre_de_tu_bd
        DB_USER=tu_usuario_bd
        DB_PASS=tu_contraseña_bd
        ```

3.  **Configurar el Back-end (PHP):**
    -   Asegúrate de que tu servidor web (ej. Apache) apunte al directorio raíz del proyecto.
    -   El back-end no requiere instalación de dependencias con Composer, ya que es PHP nativo.

4.  **Configurar y Ejecutar el Front-end (Angular):**
    -   Instala las dependencias del proyecto:
        ```bash
        npm install --prefix frontend
        ```
    -   Ejecuta el servidor de desarrollo de Angular:
        ```bash
        npm start --prefix frontend
        ```
    -   Esto iniciará la aplicación en `http://localhost:4200`. Gracias al archivo `proxy.conf.json`, todas las peticiones a `/api` serán redirigidas a tu servidor PHP local, evitando problemas de CORS.

## 3. Guía de Despliegue en Producción (CI/CD)

El proyecto está configurado con un flujo de **Integración y Despliegue Continuo (CI/CD)** a través de **GitHub Actions**, definido en `.github/workflows/deploy.yaml`.

El flujo se activa automáticamente en cada `push` a la rama `master` y realiza los siguientes pasos:
1.  **Checkout del Código:** Descarga la última versión del repositorio.
2.  **Configuración de Node.js:** Prepara el entorno para compilar el front-end.
3.  **Instalación de Dependencias:** Ejecuta `npm install` para el proyecto de Angular.
4.  **Compilación de Angular:** Compila la aplicación en modo de producción. La salida se genera en la carpeta `static/`.
5.  **Generación de `.env`:** Crea el archivo `.env` con las credenciales de la base de datos obtenidas de los *Secrets* de GitHub.
6.  **Despliegue por FTP:** Sube el contenido del proyecto (incluyendo `static/`, `api/`, `businesslogic/` y `.env`) a un servidor FTP. Las carpetas de desarrollo como `frontend/` y `node_modules/` son excluidas.
7.  **Creación de Tag:** Si el despliegue es exitoso, crea y empuja un *tag* de versión al repositorio para marcar el despliegue.

## 4. Documentación de Endpoints de la API REST

La API REST es el núcleo del back-end. Todos los *endpoints* se encuentran bajo el prefijo `/api` en un entorno de desarrollo con proxy (o directamente en producción).

---

### **Registro de Solicitudes**

-   **Endpoint:** `/register.php`
-   **Verbo:** `POST`
-   **Descripción:** Registra una nueva solicitud de asesoría.
-   **Cuerpo (Payload):**
    ```json
    {
      "nombre": "Juan Pérez",
      "correo": "juan.perez@example.com",
      "telefono": "123456789"
    }
    ```
-   **Respuesta Exitosa (201 Created):**
    ```json
    {
      "success": true,
      "message": "Solicitud registrada con éxito."
    }
    ```
-   **Respuesta de Error (400 Bad Request / 500 Internal Server Error):**
    ```json
    {
      "success": false,
      "error": "El nombre y el correo son obligatorios."
    }
    ```

---

### **Inicio de Sesión de Administrador**

-   **Endpoint:** `/login.php`
-   **Verbo:** `POST`
-   **Descripción:** Autentica a un administrador y crea una sesión.
-   **Cuerpo (Payload):**
    ```json
    {
      "usuario": "admin",
      "password": "password_seguro"
    }
    ```
-   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "message": "Login exitoso."
    }
    ```
-   **Respuesta de Error (401 Unauthorized):**
    ```json
    {
      "success": false,
      "error": "Credenciales incorrectas."
    }
    ```

---

### **Obtener Solicitudes (Protegido)**

-   **Endpoint:** `/solicitudes.php`
-   **Verbo:** `GET`
-   **Autenticación:** **Requerida**. Debe existir una sesión de administrador activa.
-   **Descripción:** Obtiene una lista de todas las solicitudes de asesoría.
-   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 1,
          "nombre": "Ana García",
          "correo": "ana.garcia@example.com",
          "telefono": "987654321",
          "estado": "pendiente",
          "created_at": "2024-01-15 10:30:00"
        }
      ]
    }
    ```
-   **Respuesta de Error (401 Unauthorized):**
    ```json
    {
      "success": false,
      "error": "Acceso no autorizado."
    }
    ```

---

### **Cierre de Sesión de Administrador**

-   **Endpoint:** `/logout.php`
-   **Verbo:** `POST`
-   **Descripción:** Cierra la sesión del administrador.
-   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "message": "Logout exitoso."
    }
    ```

## 5. Puntos de Consideración y Dependencias

-   **Seguridad:** La configuración `Access-Control-Allow-Origin: *` en los *endpoints* de la API es permisiva para facilitar el desarrollo. En un entorno de producción, se recomienda restringirla a un dominio específico.
-   **Gestión de Errores:** La API devuelve mensajes de error genéricos. Se podría implementar un sistema de logging más robusto para registrar detalles de los errores en el servidor.
-   **Variables de Entorno:** Es crucial gestionar las credenciales de la base de datos y otras claves secretas a través de variables de entorno o *secrets* del repositorio, y nunca subirlas directamente al código fuente.
-   **Versiones:**
    -   **PHP:** >= 8.0
    -   **Node.js:** >= 20.x
    -   **Angular:** ^17.x (o la versión especificada en `frontend/package.json`)
```markdown
# Easy English App (Taller 1)

***

> https://cursoingles.gt.tc/?i=1

## 📝 Descripción del Proyecto y Sector Elegido

**Nombre del Proyecto:** Easy English App

**Sector Elegido:** Servicios - Asesorías de Inglés

Este proyecto consiste en una aplicación desarrollada en **PHP** y **MySQL** que tiene como objetivo registrar clientes interesados en asesorías de inglés y permitir la administración de estos contactos a través de un Panel de Control.

### **Historias de Usuario Clave**
* Como **Usuario final**, quiero registrarme, para ser contactado para asesoría sobre el curso de inglés.
* Como **Usuario**, quiero solicitar darme de baja de la base de datos, para no recibir más información sobre los cursos.
* Como **Administrador/Asesor**, quiero consultar y ver la lista de contactos, para filtrar cuáles necesito contactar.

***

## 👨‍💻 Integrantes y Roles

El equipo cuenta con **6 integrantes**, por lo que se ha optado por combinar el rol de **Documentador/Presentador** con el **Líder / Coordinador** (ScrumMaster) para cubrir las responsabilidades completas.

| Integrante | Rol Asignado | Responsabilidades Específicas | Rol de Origen en Documento |
| :--- | :--- | :--- | :--- |
| **Alejandro Parra** | Líder / Coordinador & Documentador / Presentador | Coordina tareas, verifica checklist, sube entrega final, redacta `README.md` y prepara presentación. | ScrumMaster |
| **Yeison Liscano** | Desarrollador Backend | Valida *inputs*, implementa *prepared statements* y gestiona la lógica de servidor. | Secretario - Dev |
| **Katerine Restrepo** | Desarrollador Frontend / UI | Mejora formularios, estilos y organiza *assets* estáticos. | Arquitect On prime |
| **Daniel Mejia** | Administrador de BD (DBA) | Exporta `dump.sql`, verifica integridad y crea registros de prueba. | DB Master |
| **David Aguilar** | DevOps / Deployment | Sube archivos a InfinityFree, configura `db_connect.php` y estructura `public_html`. | Arquitect Cloud |
| **Ivan Rosero** | QA / Tester | Realiza pruebas, documenta errores y verifica correcciones. | Analista |

***

## 🚀 Instrucciones para Ejecutar Localmente

**Stack Tecnológico:** HTML, PHP, MySQL

### Requisitos
1.  Servidor local (ej: XAMPP, MAMP, WAMP) con soporte para **PHP** y **MySQL**.
2.  Importar la base de datos (`dump.sql`) a su servidor MySQL local.

### Configuración de Conexión (`db_connect.php`)

Para ejecutar la aplicación localmente, debe modificar el archivo `db_connect.php` con las credenciales de su entorno local (ej: usuario: `root`, password: `''`).

**Valores de ejemplo (de hosting) a ser reemplazados por sus valores locales:**

| Variable / Detalle | Valor a Reemplazar (Hosting) | **Valor Local Sugerido** |
| :--- | :--- | :--- |
| `DB_HOST` | `sql104.infinityfree.com` | `localhost` |
| `DB_USER` | `if0_40011703` | `root` |
| `DB_PASSWORD` | `Qd7tJPdy8pBBH` | `''` (vacío) |
| `DB_NAME` | `if0_40011703_db_easyapp` | `db_easyapp` |
| `DB_PORT` | `3306` | `3306` |

***

## 🔗 Evidencias (Enlaces y Capturas)

| Módulo | Enlace (Ejecución en Hosting) | Capturas de Pantalla (Local/Producción) |
| :--- | :--- | :--- |
| Formulario de Registro (HU1) | [https://cursoingles.gt.tc/?i=1](https://cursoingles.gt.tc/?i=1) | *(Insertar capturas del formulario de registro)* |
| Login de Administración | [https://cursoingles.gt.tc/admin/login.php](https://cursoingles.gt.tc/admin/login.php) | *(Insertar capturas del login de administración)* |

***

## 📜 Changelog (Qué hizo cada integrante)

*(Este apartado debe ser completado por el equipo al finalizar la entrega.)*

| Integrante | Tareas Realizadas (Ejemplos) |
| :--- | :--- |
| Alejandro Parra | Finalización de la redacción del `README.md`, verificación del *checklist* final. |
| Yeison Liscano | Implementación de sanitización de campos, uso de *prepared statements* para el registro de asesorías. |
| Katerine Restrepo | Diseño y mejora de estilos CSS para los formularios de registro y login. |
| Daniel Mejia | Creación del esquema MySQL (`Clientes potenciales`, `Resultados contacto`, `Asesores`), exportación de `dump.sql`. |
| David Aguilar | Despliegue en InfinityFree, configuración de las credenciales en `db_connect.php` del entorno de producción. |
| Ivan Rosero | Documentación de errores en el login (manejo incorrecto de sesiones), verificación de datos. |

***

## ❓ Respuestas a las Preguntas de Seguridad y Migración a Cloud

### 🔒 Seguridad

El proyecto contempla las siguientes consideraciones de seguridad:

* **Validación y Sanitización:** Limpieza de campos y valores para evitar la inyección de código.
* **Consultas SQL:** Se contempla *Parameterize all SQL queries* para evitar la inyección SQL.
* **Autenticación:** Uso de **Hash + salt Password** para el *login* y uso de **HTTPS** y configuración segura de sesión.
* **Contra Bots/Ataques Automatizados:** Implementación de **captcha** en el formulario de solicitud de cursos y *login*.
* **Denegación de Servicio (DoS):** Se contempla la posibilidad de reducir la respuesta a IPs comprometidas.

### ☁️ Migración a Cloud (AWS)

La migración a una plataforma *Cloud* como AWS se justifica por:

* **Escalabilidad:** Horizontal y vertical en la capacidad de la máquina virtual (almacenamiento, RAM, *cores*).
* **Seguridad Avanzada:** Uso de **IAM**, **VPC** para redes privadas, *Service Control Policies (SCP)*, y **Zero Trust**.
* **Alta Disponibilidad:** Réplicas en diferentes zonas de disponibilidad (*Availability zones*) para SLAs más altos.
* **Personalización:** Libertad para escoger lenguajes y bases de datos, permitiendo migrar hacia nuevas tecnologías.

El plan de migración más sencillo desde InfinityFree a AWS es:
* **Opción #1:** VPC - SG > Amazon Lightsail + DB MySQL dentro de la misma instancia o RDS MySQL pequeña (`db.t3.micro`).

El plan más elaborado y escalable es:
* **Opción #5:** VPC - SG > EKS + Karpenter + Aurora MySQL Serverless v2 + S3 (Integración a los *pods* vía CSI) - Cloudfront + ELB Ingress Controller + Route 53.

**Mejoras de Seguridad en Cloud Provider:**
* Implementación de seguridad mediante **Security Groups (SG)** a nivel de instancia.
* Uso de **Amazon Shield** (habilitado por defecto) para protección contra DDOS.
* Base de datos en segmento de red privado con acceso mediante *bastion host* o *Session Manager*.
* Uso de **Secrets Manager** para credenciales.
* Uso de **Cloudfront con WAF**.
```

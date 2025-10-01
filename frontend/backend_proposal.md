# Propuesta de Modificación para el Backend PHP: Endpoint de Filtros

## 1. Resumen

Para dar soporte a la nueva funcionalidad de filtrado del dashboard de administración, el endpoint de backend `GET /v1/requests` debe ser modificado para aceptar y procesar varios parámetros de consulta (*query parameters*).

Esta propuesta detalla los parámetros esperados y sugiere una estrategia de implementación en PHP para filtrar las solicitudes de asesoría desde la base de datos.

## 2. Endpoint

**Método:** `GET`
**URL:** `/v1/requests`

## 3. Parámetros de Consulta (Query Parameters)

El frontend enviará los siguientes parámetros opcionales en la URL. El backend debe estar preparado para manejar cualquier combinación de ellos.

| Parámetro               | Tipo de Dato | Formato de URL (Ejemplo)                      | Descripción                                                                 |
| ----------------------- | ------------ | --------------------------------------------- | --------------------------------------------------------------------------- |
| `nombres`               | `string`     | `?nombres=John%20Doe`                         | Filtrar por coincidencia parcial (LIKE) en el nombre del solicitante.       |
| `correo`                | `string`     | `?correo=test@example.com`                    | Filtrar por coincidencia parcial (LIKE) en el correo electrónico.           |
| `idiomas[]`             | `array`      | `?idiomas[]=Ingles&idiomas[]=Aleman`          | Filtrar solicitudes que incluyan **todos** los idiomas especificados.       |
| `contactado`            | `boolean`    | `?contactado=true`                            | Si es `true`, devolver solo las contactadas. Si es `false` o no se envía, devolver todas. |
| `fecha_contacto_inicio` | `string`     | `?fecha_contacto_inicio=2023-01-01`           | Fecha de inicio del rango para `fecha_contacto`. Formato `YYYY-MM-DD`.      |
| `fecha_contacto_fin`    | `string`     | `?fecha_contacto_fin=2023-01-31`              | Fecha de fin del rango para `fecha_contacto`. Formato `YYYY-MM-DD`.         |
| `fecha_creacion_inicio` | `string`     | `?fecha_creacion_inicio=2022-01-01`           | Fecha de inicio del rango para `fecha_creacion`. Formato `YYYY-MM-DD`.      |
| `fecha_creacion_fin`    | `string`     | `?fecha_creacion_fin=2022-12-31`              | Fecha de fin del rango para `fecha_creacion`. Formato `YYYY-MM-DD`.         |

### Ejemplo de URL Completa

Una consulta combinando varios filtros se vería así:
```
/v1/requests?nombres=Ana&idiomas[]=Ingles&contactado=true&fecha_creacion_inicio=2023-01-01
```

## 4. Estrategia de Implementación en PHP (Sugerencia)

Se recomienda construir la consulta SQL dinámicamente basándose en los parámetros recibidos en el array `$_GET`.

```php
<?php
// Asumiendo un controlador o script que maneja la ruta /v1/requests

// 1. Iniciar la consulta base
$sql = "SELECT * FROM advisory_requests WHERE 1=1";
$params = [];

// 2. Añadir condiciones dinámicamente

// Filtro por nombres (búsqueda parcial)
if (!empty($_GET['nombres'])) {
    $sql .= " AND nombres LIKE ?";
    $params[] = '%' . $_GET['nombres'] . '%';
}

// Filtro por correo (búsqueda parcial)
if (!empty($_GET['correo'])) {
    $sql .= " AND correo LIKE ?";
    $params[] = '%' . $_GET['correo'] . '%';
}

// Filtro por idiomas (asumiendo que 'idiomas' es una columna tipo JSON o texto separado por comas)
if (!empty($_GET['idiomas']) && is_array($_GET['idiomas'])) {
    foreach ($_GET['idiomas'] as $idioma) {
        // Para JSON:
        $sql .= " AND JSON_CONTAINS(idiomas, ?)";
        $params[] = json_encode($idioma);
        // Para texto (ej: "Ingles,Aleman"):
        // $sql .= " AND FIND_IN_SET(?, idiomas)";
        // $params[] = $idioma;
    }
}

// Filtro por estado "contactado"
if (isset($_GET['contactado']) && $_GET['contactado'] === 'true') {
    $sql .= " AND contactado = ?";
    $params[] = 1; // o true, dependiendo del tipo de columna
}

// Filtros de rango de fechas
if (!empty($_GET['fecha_creacion_inicio'])) {
    $sql .= " AND fecha_creacion >= ?";
    $params[] = $_GET['fecha_creacion_inicio'];
}
if (!empty($_GET['fecha_creacion_fin'])) {
    $sql .= " AND fecha_creacion <= ?";
    $params[] = $_GET['fecha_creacion_fin'];
}
// Repetir lógica similar para fecha_contacto...

// 3. Preparar y ejecutar la consulta (usando PDO como ejemplo)
// $stmt = $pdo->prepare($sql);
// $stmt->execute($params);
// $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Devolver resultados como JSON
// header('Content-Type: application/json');
// echo json_encode($results);

?>
```

Esta estructura permite añadir filtros de forma segura y mantenible, utilizando sentencias preparadas para prevenir inyecciones SQL. El equipo de backend deberá adaptar la lógica de consulta a la estructura exacta de su base de datos (ej. tipo de columna para `idiomas`).
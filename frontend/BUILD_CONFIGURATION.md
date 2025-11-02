# Configuración de Build Angular

## ✅ Cambios realizados

### 1. **angular.json**
- ✅ Cambiado de `@angular/build:application` a `@angular-devkit/build-angular:browser`
- ✅ Configurado `"outputPath": "dist"` para generar archivos en la raíz
- ✅ Eliminada la carpeta `browser/` intermedia
- ✅ Configurados assets desde carpeta `public/`

### 2. **Builder usado**
```json
"builder": "@angular-devkit/build-angular:browser"
```
Este builder genera los archivos directamente en la carpeta de salida sin crear subcarpetas adicionales.

### 3. **Estructura de salida**
```
frontend/
├── dist/
│   ├── index.html          ← Archivo principal
│   ├── main.[hash].js      ← JavaScript principal
│   ├── styles.[hash].css   ← Estilos CSS
│   ├── polyfills.[hash].js ← Polyfills
│   ├── favicon.ico         ← Favicon desde public/
│   └── otros archivos...   ← Recursos estáticos
└── src/
```

### 3. **Comandos de build**

#### Desarrollo:
```bash
cd frontend
ng build
```

#### Producción:
```bash
cd frontend
ng build --configuration production
```

#### Deploy a S3:
```bash
# Después del build de producción
aws s3 sync frontend/dist/ s3://repository-terraform-states-prod --delete
```

## 🔧 Verificación

Para verificar que la configuración funciona:

1. **Compilar el proyecto:**
   ```bash
   cd frontend
   npm install
   ng build --configuration production
   ```

2. **Verificar la estructura:**
   ```bash
   ls -la frontend/dist/
   # Deberías ver index.html directamente en dist/
   ```

3. **Verificar que no hay carpeta browser:**
   ```bash
   ls frontend/dist/browser 2>/dev/null || echo "✅ No hay carpeta browser - Configuración correcta"
   ```

## 🚀 GitHub Actions

El workflow `frontend-ci-cd.yml` está actualizado para usar `frontend/dist` como path del artifact.

## ⚠️ Nota importante

Si anteriormente tenías builds en la carpeta `browser/`, asegúrate de limpiar el directorio `dist` antes de hacer nuevos builds:

```bash
rm -rf frontend/dist
ng build --configuration production
```

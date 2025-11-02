# Configuración de Build Angular

## ✅ Cambios realizados

### 1. **angular.json**
- ✅ Agregado `"outputPath": "dist"` en la configuración de build
- ✅ Esto hace que los archivos se generen directamente en `frontend/dist/` en lugar de `frontend/dist/angular-app/browser/`

### 2. **Estructura de salida**
```
frontend/
├── dist/
│   ├── index.html          ← Archivo principal
│   ├── main-[hash].js      ← JavaScript principal
│   ├── styles-[hash].css   ← Estilos CSS
│   ├── polyfills-[hash].js ← Polyfills
│   └── assets/             ← Recursos estáticos
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

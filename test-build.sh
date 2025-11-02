#!/bin/bash

# Script para probar la configuración de build de Angular
# Uso: ./test-build.sh

set -e

echo "🚀 Iniciando test de build de Angular..."

# Navegar al directorio frontend
cd frontend

echo "📦 Instalando dependencias..."
npm install

echo "🧹 Limpiando build anterior..."
rm -rf dist

echo "🔨 Construyendo aplicación..."
./node_modules/.bin/ng build --configuration production

echo "🔍 Verificando resultado..."
echo ""
echo "=== Contenido de dist/ ==="
ls -la dist/

echo ""
echo "=== Verificando estructura ==="
if [ -f "dist/index.html" ]; then
    echo "✅ index.html encontrado en la raíz"
else
    echo "❌ index.html NO encontrado en la raíz"
    echo "Estructura completa:"
    find dist/ -type f | head -20
    exit 1
fi

echo ""
echo "=== Archivos principales ==="
ls -la dist/*.html dist/*.js dist/*.css 2>/dev/null || echo "Algunos archivos pueden no existir"

echo ""
echo "✅ Build completado exitosamente!"
echo "📁 Los archivos están listos en frontend/dist/"
echo ""
echo "🚀 Para subir a S3:"
echo "aws s3 sync frontend/dist/ s3://repository-terraform-states-prod --delete"
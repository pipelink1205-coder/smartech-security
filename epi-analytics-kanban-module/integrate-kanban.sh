#!/bin/bash

# Script de integración automática del Kanban en EPI-ANALITYCS
# Uso: ./integrate-kanban.sh

set -e

echo "🚀 Iniciando integración del Kanban en EPI-ANALITYCS..."
echo ""

# Colores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "package.json" ]; then
    echo "❌ Error: Ejecuta este script desde la raíz de EPI-ANALITYCS"
    exit 1
fi

echo "${BLUE}📁 Creando estructura de módulos...${NC}"
mkdir -p modules

echo "${BLUE}📥 Descargando módulo Kanban...${NC}"
if [ -d "temp-kanban-repo" ]; then
    rm -rf temp-kanban-repo
fi

git clone -b cursor/kanban-task-manager-1946 --depth 1 \
    https://github.com/pipelink1205-coder/smartech-security.git \
    temp-kanban-repo

echo "${BLUE}📦 Copiando archivos del Kanban...${NC}"
cp -r temp-kanban-repo/kanban-app modules/kanban

echo "${BLUE}🧹 Limpiando archivos temporales...${NC}"
rm -rf temp-kanban-repo
rm -rf modules/kanban/node_modules 2>/dev/null

echo "${BLUE}⚙️  Configurando package.json del módulo...${NC}"
cd modules/kanban

# Actualizar nombre del paquete
sed -i 's/"name": "kanban-task-manager"/"name": "epi-analytics-kanban-module"/' package.json
sed -i 's/"description": "Programador de tareas estilo Canvas con drag & drop"/"description": "Módulo Kanban para EPI-ANALITYCS Studio"/' package.json

cd ../..

echo "${BLUE}📝 Actualizando package.json raíz...${NC}"

# Verificar si ya tiene los scripts
if ! grep -q "kanban:dev" package.json; then
    # Backup del package.json original
    cp package.json package.json.backup
    
    # Agregar scripts (requiere jq)
    if command -v jq &> /dev/null; then
        jq '.scripts += {
            "kanban:install": "cd modules/kanban && npm install",
            "kanban:dev": "cd modules/kanban && npm run dev",
            "kanban:build": "cd modules/kanban && npm run build",
            "dev:all": "concurrently \"npm run dev\" \"npm run kanban:dev\""
        }' package.json > package.json.tmp && mv package.json.tmp package.json
        
        echo "${GREEN}✅ Scripts agregados al package.json${NC}"
    else
        echo "${YELLOW}⚠️  jq no está instalado. Agrega manualmente estos scripts:${NC}"
        echo '
  "kanban:install": "cd modules/kanban && npm install",
  "kanban:dev": "cd modules/kanban && npm run dev",
  "kanban:build": "cd modules/kanban && npm run build",
  "dev:all": "concurrently \"npm run dev\" \"npm run kanban:dev\""
'
    fi
else
    echo "${GREEN}✅ Scripts del Kanban ya existen${NC}"
fi

echo "${BLUE}📦 Instalando concurrently...${NC}"
npm install --save-dev concurrently

echo "${BLUE}📦 Instalando dependencias del Kanban...${NC}"
cd modules/kanban
npm install
cd ../..

echo ""
echo "${GREEN}✅ ¡Integración completada exitosamente!${NC}"
echo ""
echo "📋 Próximos pasos:"
echo ""
echo "  1. Personaliza los colores en: ${BLUE}modules/kanban/src/index.css${NC}"
echo "  2. Actualiza el título en: ${BLUE}modules/kanban/src/App.jsx${NC}"
echo ""
echo "🚀 Para ejecutar:"
echo ""
echo "  ${YELLOW}npm run kanban:dev${NC}        # Solo el Kanban"
echo "  ${YELLOW}npm run dev:all${NC}           # EPI-ANALITYCS + Kanban"
echo ""
echo "🌐 El Kanban estará disponible en: ${BLUE}http://localhost:5173${NC}"
echo ""
echo "📚 Documentación completa: ${BLUE}modules/kanban/INTEGRACION-PASO-A-PASO.md${NC}"
echo ""

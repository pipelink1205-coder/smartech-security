# 📋 Módulo Kanban para EPI-ANALITYCS

## 🎯 ¿Qué es esto?

Este es el módulo Kanban Task Manager listo para integrar en tu proyecto **EPI-ANALITYCS Studio**.

---

## ⚡ Instalación Rápida (Opción Automática)

### Opción 1: Script Automático

```bash
# 1. Copia este módulo completo a tu proyecto EPI-ANALITYCS
cp -r epi-analytics-kanban-module /ruta/a/EPI_ANALITYCS/

# 2. Ve a tu proyecto
cd /ruta/a/EPI_ANALITYCS

# 3. Ejecuta el script de integración
./epi-analytics-kanban-module/integrate-kanban.sh
```

El script hará todo automáticamente:
- ✅ Crea la carpeta `modules/kanban`
- ✅ Copia los archivos necesarios
- ✅ Actualiza el `package.json`
- ✅ Instala las dependencias
- ✅ Configura los scripts

---

## 📝 Instalación Manual (Opción Paso a Paso)

### Paso 1: Copiar el módulo

```bash
cd /ruta/a/EPI_ANALITYCS
mkdir -p modules
cp -r /ruta/a/epi-analytics-kanban-module modules/kanban
rm -rf modules/kanban/node_modules
```

### Paso 2: Actualizar package.json raíz

Agrega estos scripts en el `package.json` principal de EPI-ANALITYCS:

```json
{
  "scripts": {
    "kanban:install": "cd modules/kanban && npm install",
    "kanban:dev": "cd modules/kanban && npm run dev",
    "kanban:build": "cd modules/kanban && npm run build",
    "dev:all": "concurrently \"npm run dev\" \"npm run kanban:dev\""
  },
  "devDependencies": {
    "concurrently": "^8.2.0"
  }
}
```

### Paso 3: Instalar dependencias

```bash
# En la raíz de EPI-ANALITYCS
npm install --save-dev concurrently
npm run kanban:install
```

---

## 🚀 Uso

### Ejecutar solo el Kanban

```bash
npm run kanban:dev
```

Abre: **http://localhost:5173**

### Ejecutar EPI-ANALITYCS + Kanban simultáneamente

```bash
npm run dev:all
```

- EPI-ANALITYCS: **http://localhost:XXXX** (tu puerto)
- Kanban: **http://localhost:5173**

### Compilar para producción

```bash
npm run kanban:build
```

Los archivos compilados estarán en: `modules/kanban/dist/`

---

## 🎨 Personalización

### Cambiar colores al tema de EPI-ANALITYCS

Edita `modules/kanban/src/index.css`:

```css
body {
  /* Cambia estos colores */
  background: linear-gradient(135deg, #TU_COLOR_1 0%, #TU_COLOR_2 100%);
}
```

### Cambiar título

Edita `modules/kanban/src/App.jsx` (línea 205):

```jsx
<header className="header">
  <h1>📋 EPI-ANALITYCS - Kanban</h1>
  <p>Gestión de Tareas y Proyectos</p>
</header>
```

---

## 🔗 Integración con Backend (Opcional)

Si quieres guardar las tareas en tu base de datos de EPI-ANALITYCS en lugar de localStorage:

### 1. Crear rutas API en tu backend

```javascript
// backend/routes/kanban.js
router.get('/api/kanban/tasks', async (req, res) => {
  // Obtener tareas de la base de datos
})

router.post('/api/kanban/tasks', async (req, res) => {
  // Guardar tareas en la base de datos
})
```

### 2. Actualizar el frontend

En `modules/kanban/src/App.jsx`, reemplaza el uso de localStorage con fetch a tu API.

---

## 📁 Estructura Final

```
EPI_ANALITYCS/
├── backend/
│   └── ...
├── frontend/
│   └── ...
├── modules/
│   └── kanban/                    ← Módulo Kanban
│       ├── src/
│       │   ├── App.jsx
│       │   ├── App.css
│       │   ├── main.jsx
│       │   └── index.css
│       ├── index.html
│       ├── package.json
│       ├── vite.config.js
│       ├── integrate-kanban.sh
│       └── README.md              ← Este archivo
├── package.json                   ← Actualizado con scripts
└── README.md
```

---

## 📚 Documentación Adicional

- **INTEGRACION-PASO-A-PASO.md**: Guía detallada de integración
- **GUIA.md**: Tutorial completo de uso del Kanban
- **EJEMPLOS.md**: Casos de uso y ejemplos visuales
- **README.md**: Documentación general del Kanban

---

## ✅ Checklist de Integración

- [ ] Copiar módulo a `modules/kanban`
- [ ] Actualizar `package.json` raíz
- [ ] Instalar dependencias (`npm run kanban:install`)
- [ ] Personalizar colores y títulos
- [ ] Probar en desarrollo (`npm run kanban:dev`)
- [ ] (Opcional) Integrar con backend
- [ ] Compilar para producción

---

## 🆘 Problemas Comunes

### "npm: command not found"
- Instala Node.js desde: https://nodejs.org

### "Port 5173 is already in use"
- Cambia el puerto en `vite.config.js`:
  ```javascript
  server: {
    port: 5174  // Cambia a otro puerto
  }
  ```

### "Cannot find module"
- Reinstala dependencias:
  ```bash
  cd modules/kanban
  rm -rf node_modules
  npm install
  ```

---

## 📞 Soporte

Para más información, revisa:
- Documentación completa en los archivos `.md`
- Código fuente en `src/`

---

**¡Listo para ser parte de EPI-ANALITYCS! 🚀**

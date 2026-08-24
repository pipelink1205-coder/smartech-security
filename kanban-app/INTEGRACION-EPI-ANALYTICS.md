# 🎯 Integración del Kanban en EPI-Analytics

## 📋 Resumen
Este documento explica cómo integrar el módulo Kanban Task Manager dentro del proyecto EPI-Analytics Studio.

---

## 🏗️ Arquitectura de Integración

### Opción 1: Como Módulo Frontend Independiente (Recomendado)

El Kanban se integrará como un módulo React independiente dentro de EPI-Analytics:

```
epi-analytics/
├── backend/              # Backend existente
├── frontend/            # Frontend principal
├── modules/             # Nuevos módulos
│   └── kanban/         # ← Módulo Kanban aquí
│       ├── src/
│       ├── package.json
│       └── vite.config.js
└── docs/
```

**Ventajas:**
- ✅ No interfiere con el código existente
- ✅ Puede desarrollarse independientemente
- ✅ Fácil de mantener y actualizar
- ✅ Puede compartir componentes con el proyecto principal

---

### Opción 2: Integrado en el Frontend Principal

Integrar directamente en la aplicación principal de EPI-Analytics:

```
epi-analytics/
├── frontend/
│   ├── src/
│   │   ├── pages/
│   │   │   ├── Dashboard.jsx
│   │   │   ├── Analytics.jsx
│   │   │   └── Kanban.jsx      # ← Nueva página Kanban
│   │   ├── components/
│   │   │   └── kanban/         # ← Componentes Kanban
│   │   └── App.jsx
│   └── package.json
```

**Ventajas:**
- ✅ Una sola aplicación
- ✅ Navegación integrada
- ✅ Comparte estado y autenticación
- ✅ Mismo estilo visual

---

### Opción 3: Como Subdominio/Ruta Separada

Servir el Kanban como una ruta independiente:

```
https://epi-analytics.com/          → App principal
https://epi-analytics.com/kanban    → Módulo Kanban
```

**Ventajas:**
- ✅ Completamente independiente
- ✅ Puede tener su propia autenticación
- ✅ Fácil de desplegar por separado

---

## 🚀 Pasos de Integración

### Para Opción 1: Módulo Independiente

#### 1. Crear estructura de módulos

```bash
cd epi-analytics
mkdir -p modules/kanban
```

#### 2. Copiar el proyecto Kanban

```bash
# Desde el repo de smartech-security
git clone https://github.com/pipelink1205-coder/smartech-security.git temp-kanban
cd temp-kanban
git checkout cursor/kanban-task-manager-1946
cp -r kanban-app/* ../epi-analytics/modules/kanban/
cd ../epi-analytics
rm -rf ../temp-kanban
```

#### 3. Ajustar configuración

Editar `modules/kanban/vite.config.js`:

```javascript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  base: '/kanban/',  // Para servir desde /kanban
  server: {
    host: '0.0.0.0',
    port: 5174,  // Puerto diferente al principal
    strictPort: true
  },
  build: {
    outDir: '../../dist/kanban',  // Compilar a carpeta compartida
    emptyOutDir: true
  }
})
```

#### 4. Actualizar package.json raíz

En el `package.json` principal de EPI-Analytics:

```json
{
  "scripts": {
    "dev": "concurrently \"npm run dev:main\" \"npm run dev:kanban\"",
    "dev:main": "cd frontend && npm run dev",
    "dev:kanban": "cd modules/kanban && npm run dev",
    "build": "npm run build:main && npm run build:kanban",
    "build:main": "cd frontend && npm run build",
    "build:kanban": "cd modules/kanban && npm run build"
  },
  "devDependencies": {
    "concurrently": "^8.2.0"
  }
}
```

#### 5. Instalar dependencias

```bash
cd modules/kanban
npm install
```

---

### Para Opción 2: Integrado en Frontend Principal

#### 1. Copiar componentes al proyecto principal

```bash
cd epi-analytics/frontend/src
mkdir -p components/kanban
mkdir -p pages/kanban
```

#### 2. Copiar archivos fuente

```bash
# Copiar componente principal
cp /path/to/kanban-app/src/App.jsx pages/kanban/KanbanPage.jsx

# Copiar estilos
cp /path/to/kanban-app/src/App.css components/kanban/
cp /path/to/kanban-app/src/index.css assets/styles/kanban.css
```

#### 3. Instalar dependencias necesarias

```bash
cd epi-analytics/frontend
npm install react-beautiful-dnd lucide-react
```

#### 4. Agregar ruta en el router

En tu archivo de rutas (ej. `router.jsx` o `App.jsx`):

```javascript
import KanbanPage from './pages/kanban/KanbanPage'

// En tus rutas:
<Route path="/kanban" element={<KanbanPage />} />
```

#### 5. Agregar navegación

```javascript
// En tu menú/sidebar
<NavLink to="/kanban">
  📋 Kanban
</NavLink>
```

---

### Para Opción 3: Ruta Separada

#### 1. Compilar el Kanban

```bash
cd kanban-app
npm run build
```

#### 2. Copiar build al public

```bash
cp -r dist/* ../epi-analytics/public/kanban/
```

#### 3. Configurar servidor

En tu backend (Node.js/Express):

```javascript
app.use('/kanban', express.static(path.join(__dirname, 'public/kanban')))
```

O en Laravel (`routes/web.php`):

```php
Route::get('/kanban/{any}', function () {
    return view('kanban');
})->where('any', '.*');
```

---

## 🎨 Personalización para EPI-Analytics

### 1. Adaptar colores y tema

Editar `src/index.css`:

```css
body {
  /* Cambiar gradiente al tema de EPI-Analytics */
  background: linear-gradient(135deg, #TU_COLOR_1 0%, #TU_COLOR_2 100%);
}
```

### 2. Integrar con autenticación

Si EPI-Analytics tiene autenticación, agregar en `App.jsx`:

```javascript
import { useAuth } from '../context/AuthContext'

function App() {
  const { user } = useAuth()
  
  // Guardar tareas por usuario
  localStorage.setItem(`kanban-data-${user.id}`, JSON.stringify(data))
}
```

### 3. Integrar con backend de EPI-Analytics

Reemplazar localStorage con API:

```javascript
// En lugar de:
localStorage.setItem('kanban-data', JSON.stringify(data))

// Usar:
await fetch('/api/kanban/tasks', {
  method: 'POST',
  body: JSON.stringify(data)
})
```

### 4. Compartir componentes

Usar componentes existentes de EPI-Analytics:

```javascript
// Importar componentes compartidos
import { Button } from '../../components/ui/Button'
import { Card } from '../../components/ui/Card'
import { Header } from '../../components/layout/Header'
```

---

## 📦 Estructura Final Recomendada

```
epi-analytics/
├── backend/
│   ├── controllers/
│   │   └── KanbanController.js
│   ├── models/
│   │   └── KanbanTask.js
│   └── routes/
│       └── kanban.js
├── frontend/
│   ├── src/
│   │   ├── pages/
│   │   │   └── KanbanPage.jsx
│   │   ├── components/
│   │   │   └── kanban/
│   │   │       ├── TaskCard.jsx
│   │   │       ├── Column.jsx
│   │   │       └── Board.jsx
│   │   └── App.jsx
│   └── package.json
├── modules/              # Alternativamente
│   └── kanban/
│       ├── src/
│       ├── package.json
│       └── README.md
└── README.md
```

---

## 🔗 API Backend (Opcional)

Si quieres persistir en base de datos:

### Modelo (Sequelize/Mongoose)

```javascript
// models/KanbanTask.js
module.exports = (sequelize, DataTypes) => {
  const KanbanTask = sequelize.define('KanbanTask', {
    id: {
      type: DataTypes.UUID,
      defaultValue: DataTypes.UUIDV4,
      primaryKey: true
    },
    title: {
      type: DataTypes.STRING,
      allowNull: false
    },
    description: DataTypes.TEXT,
    priority: {
      type: DataTypes.ENUM('low', 'medium', 'high'),
      defaultValue: 'medium'
    },
    column: {
      type: DataTypes.ENUM('pending', 'in-progress', 'review', 'completed'),
      defaultValue: 'pending'
    },
    order: DataTypes.INTEGER,
    userId: DataTypes.INTEGER
  })
  
  return KanbanTask
}
```

### Rutas API

```javascript
// routes/kanban.js
router.get('/api/kanban/tasks', authenticateUser, getTasks)
router.post('/api/kanban/tasks', authenticateUser, createTask)
router.put('/api/kanban/tasks/:id', authenticateUser, updateTask)
router.delete('/api/kanban/tasks/:id', authenticateUser, deleteTask)
router.patch('/api/kanban/tasks/:id/move', authenticateUser, moveTask)
```

---

## ✅ Checklist de Integración

- [ ] Decidir opción de integración (1, 2 o 3)
- [ ] Copiar archivos del Kanban
- [ ] Instalar dependencias
- [ ] Ajustar configuración (rutas, puertos)
- [ ] Personalizar colores y estilos
- [ ] Integrar con autenticación (si aplica)
- [ ] Conectar con backend (si aplica)
- [ ] Agregar navegación/menú
- [ ] Probar en desarrollo
- [ ] Compilar para producción
- [ ] Desplegar

---

## 🆘 Ayuda

¿Necesitas ayuda con la integración? Contacta al desarrollador o revisa la documentación:

- `README.md` - Documentación general del Kanban
- `GUIA.md` - Tutorial completo
- `EJEMPLOS.md` - Casos de uso

---

**Fecha de creación:** Agosto 2026  
**Versión del Kanban:** 1.0.0  
**Compatible con:** React 18+, Vite 5+

# 📋 Integración del Kanban en EPI-ANALITYCS

## 🎯 Pasos para Integrar

### PASO 1: Agregar el módulo Kanban a tu proyecto

En tu computadora, donde tengas el proyecto EPI-ANALITYCS:

```bash
cd EPI_ANALITYCS

# Crear carpeta de módulos si no existe
mkdir -p modules

# Clonar el Kanban desde smartech-security
git clone -b cursor/kanban-task-manager-1946 https://github.com/pipelink1205-coder/smartech-security.git temp-repo

# Copiar solo la carpeta kanban-app
cp -r temp-repo/kanban-app modules/kanban

# Limpiar
rm -rf temp-repo
```

---

### PASO 2: Configurar el Kanban para EPI-ANALITYCS

Edita `modules/kanban/package.json` y cambia el nombre:

```json
{
  "name": "epi-analytics-kanban-module",
  "version": "1.0.0",
  "description": "Módulo Kanban para EPI-ANALITYCS Studio",
  ...
}
```

---

### PASO 3: Personalizar colores (Opcional)

Edita `modules/kanban/src/index.css` para que coincida con los colores de EPI-ANALITYCS:

```css
body {
  /* Cambia estos colores al tema de EPI-ANALITYCS */
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}
```

---

### PASO 4: Actualizar el título

Edita `modules/kanban/index.html`:

```html
<title>Kanban - EPI-ANALITYCS Studio</title>
```

Y en `modules/kanban/src/App.jsx`, cambia el header:

```jsx
<header className="header">
  <h1>📋 EPI-ANALITYCS - Kanban</h1>
  <p>Gestión de Tareas y Proyectos</p>
</header>
```

---

### PASO 5: Configurar scripts en el package.json raíz

En el `package.json` principal de EPI-ANALITYCS, agrega:

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

---

### PASO 6: Instalar dependencias

```bash
# Instalar concurrently para ejecutar ambos proyectos
npm install --save-dev concurrently

# Instalar dependencias del Kanban
npm run kanban:install
```

---

### PASO 7: Ejecutar en desarrollo

```bash
# Solo el Kanban
npm run kanban:dev

# O ambos proyectos al mismo tiempo
npm run dev:all
```

El Kanban estará disponible en: **http://localhost:5173**

---

## 🔗 Integración con el Backend (Opcional)

Si quieres que el Kanban guarde datos en tu base de datos de EPI-ANALITYCS:

### 1. Crear modelo en el backend

```javascript
// backend/models/KanbanTask.js
const KanbanTask = {
  id: String,
  title: String,
  description: String,
  priority: String, // 'low', 'medium', 'high'
  column: String,   // 'pending', 'in-progress', 'review', 'completed'
  userId: String,
  projectId: String, // Opcional: vincular a proyectos de EPI
  createdAt: Date,
  updatedAt: Date
}
```

### 2. Crear rutas API

```javascript
// backend/routes/kanban.js
router.get('/api/kanban/tasks', getTasks)
router.post('/api/kanban/tasks', createTask)
router.put('/api/kanban/tasks/:id', updateTask)
router.delete('/api/kanban/tasks/:id', deleteTask)
```

### 3. Actualizar el frontend del Kanban

En `modules/kanban/src/App.jsx`, reemplaza localStorage con llamadas API:

```javascript
// En lugar de:
useEffect(() => {
  localStorage.setItem('kanban-data', JSON.stringify(data))
}, [data])

// Usa:
useEffect(() => {
  const saveData = async () => {
    await fetch('http://localhost:3000/api/kanban/tasks', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
  }
  saveData()
}, [data])
```

---

## 📱 Despliegue

### Compilar para producción

```bash
npm run kanban:build
```

Esto creará una carpeta `modules/kanban/dist/` con los archivos compilados.

### Servir desde tu servidor

Copia los archivos compilados a tu servidor web:

```bash
# Si usas un servidor estático
cp -r modules/kanban/dist/* public/kanban/

# Si usas Express
app.use('/kanban', express.static('modules/kanban/dist'))
```

Acceso: **https://tu-dominio.com/kanban**

---

## 🎨 Personalización Avanzada

### Integrar con sistema de usuarios

```javascript
// modules/kanban/src/App.jsx
import { useAuth } from '../../../src/context/AuthContext'

function App() {
  const { user } = useAuth()
  const storageKey = `kanban-data-${user.id}`
  
  const [data, setData] = useState(() => {
    const saved = localStorage.getItem(storageKey)
    return saved ? JSON.parse(saved) : INITIAL_DATA
  })
}
```

### Usar componentes de EPI-ANALITYCS

```javascript
// Importar componentes compartidos
import { Button } from '../../../src/components/Button'
import { Card } from '../../../src/components/Card'
```

---

## 📊 Estructura Final

```
EPI_ANALITYCS/
├── backend/
│   ├── models/
│   │   └── KanbanTask.js
│   └── routes/
│       └── kanban.js
├── frontend/
│   └── src/
│       └── ...
├── modules/
│   └── kanban/                    ← Nuevo módulo
│       ├── src/
│       │   ├── App.jsx
│       │   ├── App.css
│       │   ├── main.jsx
│       │   └── index.css
│       ├── index.html
│       ├── package.json
│       ├── vite.config.js
│       └── README.md
├── package.json                   ← Scripts actualizados
└── README.md
```

---

## ✅ Checklist

- [ ] Crear carpeta `modules/kanban`
- [ ] Copiar archivos del Kanban
- [ ] Personalizar colores y títulos
- [ ] Actualizar package.json raíz
- [ ] Instalar dependencias
- [ ] Probar en desarrollo (`npm run kanban:dev`)
- [ ] (Opcional) Integrar con backend
- [ ] (Opcional) Integrar con autenticación
- [ ] Compilar para producción
- [ ] Desplegar

---

## 🚀 Comandos Rápidos

```bash
# Desarrollo
npm run kanban:dev                 # Solo Kanban
npm run dev:all                    # EPI-ANALITYCS + Kanban

# Producción
npm run kanban:build               # Compilar Kanban

# Mantenimiento
cd modules/kanban && npm install   # Actualizar dependencias
```

---

**¡El Kanban está listo para ser parte de EPI-ANALITYCS! 🎉**

# 🎯 Guía Completa del Kanban Task Manager

## 📖 Introducción

Esta aplicación es un programador de tareas estilo Canvas/Kanban que te permite organizar tu trabajo de forma visual e intuitiva.

## 🚀 Inicio Rápido

### Instalación

```bash
cd kanban-app
npm install
npm run dev
```

La aplicación estará disponible en: http://localhost:5173

## 💡 Casos de Uso

### 1. Gestión de Proyectos Personales
Organiza tus proyectos personales dividiendo el trabajo en tareas y moviendo entre estados.

### 2. Seguimiento de Errores (Bug Tracking)
- **Pendiente**: Nuevos bugs reportados
- **En Progreso**: Bugs siendo investigados
- **En Revisión**: Fix listo para testing
- **Completado**: Bug resuelto

### 3. Planificación de Sprints
Usa las columnas para representar el flujo de trabajo de tu equipo ágil.

### 4. Lista de Tareas Personales (To-Do)
Simple sistema de tareas para organizar tu día a día.

## 🎨 Personalización

### Cambiar Colores del Gradiente

Edita `src/index.css`:

```css
body {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

Prueba estas combinaciones:
- Azul/Verde: `linear-gradient(135deg, #0093E9 0%, #80D0C7 100%)`
- Naranja/Rosa: `linear-gradient(135deg, #FBDA61 0%, #FF5ACD 100%)`
- Verde/Azul: `linear-gradient(135deg, #21D4FD 0%, #B721FF 100%)`

### Agregar Más Columnas

Edita el `INITIAL_DATA` en `src/App.jsx`:

```javascript
const INITIAL_DATA = {
  columns: {
    'backlog': {
      id: 'backlog',
      title: 'Backlog',
      taskIds: []
    },
    'pending': {
      id: 'pending',
      title: 'Pendiente',
      taskIds: []
    },
    // ... más columnas
  },
  columnOrder: ['backlog', 'pending', 'in-progress', 'review', 'completed']
}
```

### Modificar Colores de Prioridad

Edita la función `getPriorityColor` en `src/App.jsx`:

```javascript
const getPriorityColor = (priority) => {
  const colors = {
    low: '#10b981',      // Verde
    medium: '#f59e0b',   // Amarillo
    high: '#ef4444',     // Rojo
    urgent: '#9333ea'    // Púrpura (nuevo)
  }
  return colors[priority] || colors.medium
}
```

## 🔧 Funcionalidades Avanzadas

### Persistencia de Datos

Los datos se guardan automáticamente en `localStorage`. Para resetear:

1. Abre las DevTools del navegador (F12)
2. Ve a "Application" > "Local Storage"
3. Elimina la entrada `kanban-data`
4. Recarga la página

### Atajos de Teclado (Futura mejora)

Ideas para implementar:
- `Ctrl + N`: Nueva tarea
- `Delete`: Eliminar tarea seleccionada
- `Esc`: Cancelar edición

### Exportar/Importar Datos

Para respaldar tus datos:

```javascript
// Exportar
const data = localStorage.getItem('kanban-data')
console.log(data)

// Importar
localStorage.setItem('kanban-data', tusDatosGuardados)
```

## 📱 Uso en Móvil

La aplicación es completamente responsive:
- **Swipe**: Desliza para ver más columnas
- **Tap & Hold**: Mantén presionado para arrastrar tareas
- **Modo Portrait**: Las columnas se apilan verticalmente

## 🔒 Seguridad y Privacidad

- ✅ Todos los datos se almacenan localmente en tu navegador
- ✅ No se envía información a servidores externos
- ✅ No requiere registro ni inicio de sesión
- ✅ Sin cookies de seguimiento

## 🐛 Solución de Problemas

### El drag & drop no funciona

**Solución**: Asegúrate de que estás usando un navegador moderno (Chrome, Firefox, Safari actualizados)

### Las tareas no se guardan

**Solución**: Verifica que tu navegador no esté en modo incógnito, ya que `localStorage` puede estar deshabilitado

### La aplicación no carga

**Solución**: 
```bash
# Limpia node_modules y reinstala
rm -rf node_modules package-lock.json
npm install
npm run dev
```

## 🎓 Tutoriales de Uso

### Tutorial 1: Crear tu primera tarea

1. Click en "+ Nueva Tarea" en la columna "Pendiente"
2. Escribe "Probar el Kanban"
3. Agrega descripción: "Primera tarea de prueba"
4. Selecciona prioridad "Alta"
5. Click en "Agregar Tarea"

### Tutorial 2: Organizar un proyecto

1. Crea tareas para cada funcionalidad en "Pendiente"
2. Cuando empieces a trabajar, arrastra la tarea a "En Progreso"
3. Al terminar, muévela a "En Revisión"
4. Después de revisar, llévala a "Completado"

### Tutorial 3: Gestión de prioridades

- **Alta (Rojo)**: Tareas urgentes que deben hacerse hoy
- **Media (Amarillo)**: Tareas importantes para esta semana
- **Baja (Verde)**: Tareas que pueden esperar

## 🚀 Mejoras Futuras

Ideas para extender la aplicación:

1. **Backend con API REST**
   - Almacenamiento en base de datos
   - Sincronización entre dispositivos

2. **Colaboración Multi-usuario**
   - Compartir tableros
   - Asignar tareas a usuarios

3. **Más Funcionalidades**
   - Fechas de vencimiento
   - Etiquetas personalizadas
   - Archivos adjuntos
   - Comentarios en tareas
   - Filtros y búsqueda

4. **Notificaciones**
   - Recordatorios de tareas
   - Alertas de vencimiento

5. **Estadísticas**
   - Gráficos de productividad
   - Tiempo por tarea
   - Tareas completadas por día/semana

6. **Temas**
   - Modo oscuro/claro
   - Temas personalizables

## 📚 Recursos Adicionales

- [React Beautiful DND Docs](https://github.com/atlassian/react-beautiful-dnd)
- [React Official Docs](https://react.dev)
- [Vite Documentation](https://vitejs.dev)

## 🤝 Contribuciones

Ideas para contribuir:
1. Mejorar la UI/UX
2. Agregar tests unitarios
3. Implementar las mejoras futuras
4. Optimizar el rendimiento
5. Traducir a más idiomas

## 📝 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

---

**¡Disfruta organizando tus tareas! 🎉**

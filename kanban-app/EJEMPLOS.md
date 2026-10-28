# 📸 Screenshots y Ejemplos

## Vista Principal

La aplicación muestra 4 columnas principales:

```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│  Pendiente  │ En Progreso │ En Revisión │ Completado  │
│     (3)     │     (1)     │     (2)     │     (0)     │
├─────────────┼─────────────┼─────────────┼─────────────┤
│             │             │             │             │
│  [Tarea 1]  │  [Tarea 4]  │  [Tarea 6]  │             │
│  [Tarea 2]  │             │  [Tarea 7]  │             │
│  [Tarea 3]  │             │             │             │
│             │             │             │             │
│ + Nueva     │ + Nueva     │ + Nueva     │ + Nueva     │
│   Tarea     │   Tarea     │   Tarea     │   Tarea     │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

## Ejemplos de Uso

### Ejemplo 1: Desarrollo de Software

**Pendiente:**
- 🔴 Corregir bug crítico en login
- 🟡 Implementar búsqueda de usuarios
- 🟢 Actualizar documentación

**En Progreso:**
- 🔴 Optimizar queries de base de datos

**En Revisión:**
- 🟡 Nueva interfaz de dashboard
- 🟡 Sistema de notificaciones

**Completado:**
- ✅ Configurar CI/CD
- ✅ Implementar autenticación JWT

### Ejemplo 2: Contenido y Marketing

**Pendiente:**
- 🔴 Escribir post sobre nueva feature
- 🟡 Crear video tutorial
- 🟢 Diseñar infografía

**En Progreso:**
- 🟡 Grabar episodio de podcast

**En Revisión:**
- 🟡 Newsletter semanal
- 🔴 Campaña en redes sociales

**Completado:**
- ✅ Publicar actualización en blog
- ✅ Enviar correos a clientes

### Ejemplo 3: Estudios y Aprendizaje

**Pendiente:**
- 🔴 Estudiar para examen de matemáticas
- 🟡 Leer capítulos 5-7 de historia
- 🟢 Hacer ejercicios de inglés

**En Progreso:**
- 🔴 Trabajar en proyecto final

**En Revisión:**
- 🟡 Revisar apuntes con compañero
- 🟡 Practicar presentación

**Completado:**
- ✅ Entregar ensayo de literatura
- ✅ Completar tarea de física

## Tarjeta de Tarea - Anatomía

```
┌────────────────────────────────────────┐
│ Corregir bug en login         [ALTA]   │ ← Título + Prioridad
├────────────────────────────────────────┤
│ El formulario de login no valida      │
│ correctamente los campos vacíos.       │ ← Descripción
│ Agregar validación del lado cliente.   │
├────────────────────────────────────────┤
│                            [✏️] [🗑️]   │ ← Acciones
└────────────────────────────────────────┘
```

## Formulario de Nueva Tarea

```
┌────────────────────────────────────────┐
│ ┌────────────────────────────────────┐ │
│ │ Título de la tarea...              │ │
│ └────────────────────────────────────┘ │
│                                        │
│ ┌────────────────────────────────────┐ │
│ │ Descripción (opcional)             │ │
│ │                                    │ │
│ └────────────────────────────────────┘ │
│                                        │
│ ┌────────────────────────────────────┐ │
│ │ Prioridad Media          ▼         │ │
│ └────────────────────────────────────┘ │
│                                        │
│ [Agregar Tarea]     [Cancelar]        │
└────────────────────────────────────────┘
```

## Paleta de Colores

### Prioridades
- 🔴 **Alta**: #ef4444 (Rojo)
- 🟡 **Media**: #f59e0b (Amarillo/Ámbar)
- 🟢 **Baja**: #10b981 (Verde)

### Gradiente Principal
- **Inicio**: #667eea (Púrpura claro)
- **Fin**: #764ba2 (Púrpura oscuro)

### UI Elements
- **Botones primarios**: #667eea
- **Bordes**: #e5e7eb (Gris claro)
- **Texto principal**: #1f2937 (Gris oscuro)
- **Texto secundario**: #6b7280 (Gris medio)

## Estados Visuales

### 1. Normal
```
┌────────────────────┐
│ Tarea normal       │
│ Descripción aquí   │
│            [✏️][🗑️]│
└────────────────────┘
```

### 2. Hover (Mouse encima)
```
┌────────────────────┐
│ Tarea con hover    │ ← Elevación visual
│ Descripción aquí   │    (sombra más grande)
│            [✏️][🗑️]│
└────────────────────┘
  └─ sombra aumenta ─┘
```

### 3. Dragging (Arrastrando)
```
   ┌────────────────┐
  ╱│ Tarea arrastr. │╲  ← Rotación leve
 ╱ │ Descripción    │ ╲    + sombra grande
╱  │        [✏️][🗑️]│  ╲
└───────────────────────┘
```

### 4. Columna con Drag Over
```
╔═════════════════════╗
║   En Progreso       ║
║        (2)          ║
╠═════════════════════╣
║ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ ║ ← Fondo azul claro
║ ▓ [Tarea 1]       ▓ ║    indica zona activa
║ ▓                 ▓ ║    para soltar
║ ▓ [Tarea 2]       ▓ ║
║ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ ║
║                     ║
║     + Nueva Tarea   ║
╚═════════════════════╝
```

## Responsive Design

### Desktop (> 768px)
```
┌──────────┬──────────┬──────────┬──────────┐
│ Columna1 │ Columna2 │ Columna3 │ Columna4 │
└──────────┴──────────┴──────────┴──────────┘
```

### Tablet (768px)
```
┌──────────┬──────────┐
│ Columna1 │ Columna2 │
├──────────┼──────────┤
│ Columna3 │ Columna4 │
└──────────┴──────────┘
```

### Mobile (< 768px)
```
┌────────────┐
│  Columna1  │
├────────────┤
│  Columna2  │
├────────────┤
│  Columna3  │
├────────────┤
│  Columna4  │
└────────────┘
```

## Tips de Diseño

### ✨ Animaciones
- Transiciones suaves de 0.2s en hover
- Rotación de 5° al arrastrar tarjetas
- Elevación gradual con sombras

### 🎨 Consistencia Visual
- Bordes redondeados de 8-12px
- Padding consistente de 0.75-1rem
- Espaciado entre elementos de 0.5-0.75rem

### 📱 Touch-Friendly
- Botones mínimo 44x44px
- Áreas de tap generosas
- Feedback visual inmediato

## Demo Data (Para Testing)

Puedes usar este código para llenar el tablero con datos de prueba:

```javascript
const DEMO_DATA = {
  columns: {
    'pending': {
      id: 'pending',
      title: 'Pendiente',
      taskIds: ['task-1', 'task-2', 'task-3']
    },
    'in-progress': {
      id: 'in-progress',
      title: 'En Progreso',
      taskIds: ['task-4']
    },
    'review': {
      id: 'review',
      title: 'En Revisión',
      taskIds: ['task-5', 'task-6']
    },
    'completed': {
      id: 'completed',
      title: 'Completado',
      taskIds: ['task-7', 'task-8']
    }
  },
  tasks: {
    'task-1': {
      id: 'task-1',
      title: 'Diseñar nueva landing page',
      description: 'Crear mockups en Figma para la nueva página principal',
      priority: 'high'
    },
    'task-2': {
      id: 'task-2',
      title: 'Actualizar dependencias',
      description: 'Revisar y actualizar paquetes npm',
      priority: 'medium'
    },
    // ... más tareas
  }
}
```

---

**💡 Tip**: Usa las DevTools del navegador para inspeccionar los estilos y experimentar con cambios en tiempo real.

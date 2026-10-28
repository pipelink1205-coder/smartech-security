# 📋 Kanban - Programador de Tareas

Una aplicación moderna de gestión de tareas estilo Canvas/Kanban con funcionalidad drag & drop.

## ✨ Características

- 🎯 **4 Columnas predefinidas**: Pendiente, En Progreso, En Revisión, Completado
- 🎨 **Interfaz moderna y responsive**: Diseño hermoso con gradientes y animaciones
- ✨ **Drag & Drop**: Arrastra tareas entre columnas fácilmente
- 💾 **Persistencia automática**: Tus tareas se guardan en localStorage
- 🏷️ **Prioridades**: Marca tareas como Alta, Media o Baja prioridad
- ✏️ **Edición completa**: Agrega, edita y elimina tareas
- 📱 **Totalmente responsive**: Funciona perfectamente en móviles y tablets

## 🚀 Instalación

```bash
# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm run dev

# Construir para producción
npm run build
```

## 🎮 Uso

1. **Agregar tarea**: Haz clic en "+ Nueva Tarea" en cualquier columna
2. **Mover tarea**: Arrastra y suelta las tareas entre columnas
3. **Editar tarea**: Haz clic en el ícono de lápiz
4. **Eliminar tarea**: Haz clic en el ícono de basura
5. **Prioridades**: Asigna prioridad Baja (verde), Media (amarillo) o Alta (rojo)

## 🛠️ Tecnologías

- **React 18**: Framework principal
- **Vite**: Build tool ultrarrápido
- **react-beautiful-dnd**: Librería para drag & drop
- **lucide-react**: Iconos modernos
- **localStorage**: Persistencia de datos

## 📝 Estructura del Proyecto

```
kanban-app/
├── src/
│   ├── App.jsx          # Componente principal
│   ├── App.css          # Estilos de la aplicación
│   ├── main.jsx         # Punto de entrada
│   └── index.css        # Estilos globales
├── index.html           # HTML base
├── package.json         # Dependencias
├── vite.config.js       # Configuración de Vite
└── README.md           # Este archivo
```

## 🎨 Personalización

Puedes personalizar fácilmente:
- Colores del gradiente en `index.css`
- Nombres de las columnas en `INITIAL_DATA`
- Colores de prioridad en la función `getPriorityColor()`
- Agregar más columnas modificando el objeto `columns`

## 📄 Licencia

MIT - Siéntete libre de usar este proyecto como quieras.

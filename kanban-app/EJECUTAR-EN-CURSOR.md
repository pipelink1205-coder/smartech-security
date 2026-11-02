# 🚀 Guía Rápida: Ejecutar Kanban en Cursor Desktop

## 📋 Requisitos Previos

- ✅ **Cursor Desktop** instalado
- ✅ **Node.js** (versión 16 o superior) - [Descargar aquí](https://nodejs.org)
- ✅ **Git** instalado

---

## 🎯 Opción 1: Clonar desde GitHub (Recomendado)

### Paso 1: Abrir terminal en Cursor

Presiona `` Ctrl+` `` (o `` Cmd+` `` en Mac) para abrir la terminal integrada.

### Paso 2: Clonar el repositorio

```bash
git clone https://github.com/pipelink1205-coder/smartech-security.git
cd smartech-security
```

### Paso 3: Cambiar a la rama del Kanban

```bash
git checkout cursor/kanban-task-manager-1946
```

### Paso 4: Abrir la carpeta del Kanban en Cursor

En Cursor Desktop:
1. `File` → `Open Folder` (o `Archivo` → `Abrir Carpeta`)
2. Navega a: `smartech-security/kanban-app`
3. Haz clic en "Abrir"

### Paso 5: Instalar dependencias

En la terminal de Cursor:

```bash
npm install
```

Esto tomará unos segundos (descarga ~85 paquetes).

### Paso 6: Ejecutar el proyecto

```bash
npm run dev
```

### Paso 7: Abrir en el navegador

Deberías ver algo como:

```
VITE v5.4.21  ready in 119 ms

➜  Local:   http://localhost:5173/
```

**Abre http://localhost:5173 en tu navegador** 🎉

---

## 🎯 Opción 2: Descargar solo el Kanban

Si solo quieres la aplicación Kanban sin todo el proyecto Laravel:

### Desde GitHub (Web):

1. Ve a: https://github.com/pipelink1205-coder/smartech-security
2. Cambia a la rama: `cursor/kanban-task-manager-1946`
3. Navega a la carpeta `kanban-app`
4. Click en `Code` → `Download ZIP`
5. Extrae el ZIP
6. Abre la carpeta en Cursor
7. Ejecuta:
   ```bash
   npm install
   npm run dev
   ```

---

## 🎯 Opción 3: Crear proyecto desde cero

Si prefieres empezar limpio en Cursor Desktop:

### 1. Crea una carpeta nueva

```bash
mkdir mi-kanban
cd mi-kanban
```

### 2. Abre la carpeta en Cursor

`File` → `Open Folder`

### 3. Clona solo la carpeta kanban-app

```bash
git init
git remote add origin https://github.com/pipelink1205-coder/smartech-security.git
git config core.sparseCheckout true
echo "kanban-app/*" >> .git/info/sparse-checkout
git pull origin cursor/kanban-task-manager-1946
cd kanban-app
```

### 4. Instala y ejecuta

```bash
npm install
npm run dev
```

---

## 🔧 Solución de Problemas

### ❌ "npm: command not found"

**Problema:** Node.js no está instalado.

**Solución:**
- **Windows:** Descarga desde https://nodejs.org
- **Mac:** `brew install node` o descarga desde https://nodejs.org
- **Linux:** `sudo apt install nodejs npm`

Luego reinicia Cursor Desktop.

---

### ❌ "Port 5173 is already in use"

**Problema:** El puerto ya está ocupado.

**Solución:**
```bash
# Mata el proceso en el puerto 5173
# Windows:
netstat -ano | findstr :5173
taskkill /PID <número> /F

# Mac/Linux:
lsof -ti:5173 | xargs kill -9

# O simplemente cambia el puerto
npm run dev -- --port 3000
```

---

### ❌ "Cannot find module"

**Problema:** Las dependencias no están instaladas.

**Solución:**
```bash
rm -rf node_modules package-lock.json
npm install
```

---

## 📱 Ver en tu celular (mismo WiFi)

1. Ejecuta `npm run dev`
2. Busca la línea que dice `Network: http://192.168.x.x:5173`
3. Abre esa URL en tu celular

**Nota:** Tu computadora y celular deben estar en la misma red WiFi.

---

## 🎨 Personalizar la Aplicación

Una vez que esté corriendo, puedes modificar:

- **Colores:** Edita `src/index.css` (línea 12)
- **Columnas:** Edita `src/App.jsx` (línea 6-31)
- **Textos:** Edita `src/App.jsx` (búsca los strings)

Los cambios se verán **automáticamente** en el navegador gracias a Hot Module Replacement (HMR).

---

## 📚 Comandos Útiles

```bash
# Ejecutar en desarrollo
npm run dev

# Compilar para producción
npm run build

# Vista previa de la versión compilada
npm run preview

# Instalar dependencias
npm install

# Limpiar y reinstalar
rm -rf node_modules package-lock.json && npm install
```

---

## ✅ Checklist de Éxito

- [ ] Node.js instalado (verifica con `node --version`)
- [ ] Cursor Desktop abierto
- [ ] Carpeta `kanban-app` abierta en Cursor
- [ ] Terminal abierta (`` Ctrl+` ``)
- [ ] Ejecutado `npm install`
- [ ] Ejecutado `npm run dev`
- [ ] Navegador abierto en http://localhost:5173
- [ ] ¡Aplicación funcionando! 🎉

---

## 💡 Tips de Cursor

### Comandos útiles en Cursor:

- `` Ctrl+` ``: Abrir/cerrar terminal
- `Ctrl+P`: Buscar archivos rápidamente
- `Ctrl+Shift+F`: Buscar en todos los archivos
- `F5`: Recargar ventana si algo falla
- `Ctrl+K Ctrl+T`: Cambiar tema

### Usar Cursor AI:

Puedes pedirle a Cursor AI que te ayude:
- "Cambia el color del gradiente a azul"
- "Agrega una columna de 'Archivado'"
- "Traduce la app al inglés"

---

## 🆘 ¿Necesitas Ayuda?

Si algo no funciona:

1. Verifica que Node.js esté instalado: `node --version`
2. Verifica que estés en la carpeta correcta: `pwd` (debería terminar en `/kanban-app`)
3. Revisa los errores en la terminal
4. Busca en `README.md`, `GUIA.md` o `EJEMPLOS.md`

---

**¡Listo para programar! 🚀**

# 🎯 INSTRUCCIONES SIMPLES PARA DESCARGAR Y EJECUTAR EL KANBAN

## 📥 OPCIÓN 1: Descargar SOLO el Kanban (MÁS FÁCIL)

### Paso 1: Abre tu navegador web
Ve a esta URL:
```
https://github.com/pipelink1205-coder/smartech-security/tree/cursor/kanban-task-manager-1946
```

### Paso 2: Descarga la carpeta kanban-app
1. Click en la carpeta `kanban-app`
2. Click en el botón verde "Code" ▼
3. Selecciona "Download ZIP"
4. Guarda el archivo en tu computadora

### Paso 3: Extraer y abrir
1. Descomprime el archivo ZIP
2. Abre **Cursor Desktop**
3. `File` → `Open Folder`
4. Selecciona la carpeta `kanban-app` que acabas de extraer

### Paso 4: Instalar y ejecutar
Abre la terminal en Cursor (`` Ctrl+` ``) y escribe:

```bash
npm install
npm run dev
```

### Paso 5: Abrir en el navegador
Abre: **http://localhost:5173**

---

## 📥 OPCIÓN 2: Clonar todo el repositorio

Si prefieres usar Git:

### Paso 1: Abrir terminal
En Cursor Desktop, presiona `` Ctrl+` ``

### Paso 2: Clonar
```bash
git clone https://github.com/pipelink1205-coder/smartech-security.git
cd smartech-security
git checkout cursor/kanban-task-manager-1946
cd kanban-app
```

### Paso 3: Ejecutar
```bash
npm install
npm run dev
```

### Paso 4: Abrir
**http://localhost:5173**

---

## 📥 OPCIÓN 3: Crear proyecto completamente nuevo (INDEPENDIENTE)

Si NO quieres que esté mezclado con SmartEdge Security:

### Paso 1: Crear carpeta nueva
```bash
mkdir mi-kanban
cd mi-kanban
```

### Paso 2: Abrir en Cursor
`File` → `Open Folder` → Selecciona `mi-kanban`

### Paso 3: Descargar solo el kanban-app
Usa estos comandos en la terminal de Cursor:

```bash
# Opción A: Con git (más rápido)
git init
git remote add origin https://github.com/pipelink1205-coder/smartech-security.git
git config core.sparseCheckout true
echo "kanban-app/*" > .git/info/sparse-checkout
git pull origin cursor/kanban-task-manager-1946
cd kanban-app

# Opción B: Descarga manual
# Ve a: https://github.com/pipelink1205-coder/smartech-security/tree/cursor/kanban-task-manager-1946/kanban-app
# Descarga los archivos manualmente
```

### Paso 4: Ejecutar
```bash
npm install
npm run dev
```

---

## ❓ ¿CUÁL OPCIÓN USAR?

### Si solo quieres el Kanban:
👉 **OPCIÓN 1** (más fácil) o **OPCIÓN 3** (más limpio)

### Si quieres todo el proyecto SmartEdge + Kanban:
👉 **OPCIÓN 2**

---

## 🔧 REQUISITOS

Antes de cualquier opción, asegúrate de tener:

✅ **Node.js** instalado (versión 16+)
   - Descarga: https://nodejs.org
   - Verifica: `node --version`

✅ **Cursor Desktop** instalado
   - Descarga: https://cursor.sh

✅ **Git** (solo para opciones 2 y 3)
   - Descarga: https://git-scm.com

---

## 📱 ACCEDER DESDE EL CELULAR

### Mientras está corriendo en tu computadora:

1. Tu computadora y celular deben estar en el **mismo WiFi**

2. Cuando ejecutes `npm run dev`, verás algo como:
   ```
   ➜  Local:   http://localhost:5173/
   ➜  Network: http://192.168.1.100:5173/
   ```

3. Abre la URL "Network" en tu celular

---

## 🆘 PROBLEMAS COMUNES

### "npm: command not found"
👉 Node.js no está instalado. Descárgalo de: https://nodejs.org

### "Port 5173 is already in use"
👉 Cambia el puerto:
```bash
npm run dev -- --port 3000
```

### "Cannot find module"
👉 Reinstala las dependencias:
```bash
rm -rf node_modules
npm install
```

---

## 📍 RESUMEN DE UBICACIONES

### En GitHub:
- **Repositorio**: https://github.com/pipelink1205-coder/smartech-security
- **Rama**: cursor/kanban-task-manager-1946
- **Carpeta**: kanban-app/
- **Pull Request**: https://github.com/pipelink1205-coder/smartech-security/pull/1

### Acceso desde web (temporal):
- **URL pública**: https://rare-tools-cheer.loca.lt
  (funciona solo mientras esté corriendo aquí)

---

## ✅ CHECKLIST

Antes de empezar:
- [ ] Node.js instalado
- [ ] Cursor Desktop instalado
- [ ] Decidiste qué opción usar (1, 2 o 3)

Durante la instalación:
- [ ] Archivos descargados/clonados
- [ ] Terminal abierta en la carpeta correcta
- [ ] `npm install` ejecutado exitosamente
- [ ] `npm run dev` ejecutado exitosamente

¡Listo para usar!:
- [ ] http://localhost:5173 abierto en el navegador
- [ ] La aplicación Kanban está visible
- [ ] Puedes crear y mover tareas

---

**¿Tienes dudas? Revisa los archivos:**
- `README.md` - Documentación general
- `GUIA.md` - Tutorial completo
- `EJEMPLOS.md` - Casos de uso

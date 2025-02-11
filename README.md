# 📊 Twitch Analytics API

Este proyecto proporciona una API RESTful que permite acceder a información sobre streams y streamers de Twitch, usando datos almacenados en una base de datos MySQL.

## 📂 Estructura del Proyecto

El proyecto contiene los siguientes archivos y carpetas:

- ⚙️ **.htaccess**: Configura el servidor para que todas las solicitudes se redirijan a `index.php`, habilitando rutas amigables.
- 📄 **Entrega 1.pdf**:  Documentación sobre todas las decisiones y problemas encontrados así como, información relativa a cómo probar la implementación y dónde se encuentra alojada.
- 🐘 **index.php**: El punto de entrada principal de la API. Define las rutas y maneja las solicitudes HTTP.
- 🔒 **cacert.pem**: Archivo que almacena certificados de autoridades de certificación (CA).
- ℹ️ **README.md**: Archivo de texto que proporciona información sobre la estructura del proyecto y su ejecución.
### 📁 Carpeta `src/`
- 🐘 **streamController.php**: Contiene la clase `StreamController`, que proporciona métodos para obtener información sobre los streams.
- 🐘 **streamerController.php**: Contiene la clase `StreamerController`, que proporciona un método para obtener información detallada sobre un streamer.
  
# 📌 Guía para ejecutarlo en local

## 📥 1. Clonar el Repositorio

Primero, debes descargar el código desde GitHub. Abre una terminal y ejecuta:

```bash
git clone https://github.com/AitorGH13/twitchapi.git
```
```bsh
cd twitchapi
```
## 🔧 2. Configurar el Entorno

Necesitas PHP y cURL instalados en tu sistema. 

```bash
sudo apt install php
```
```bash
sudo apt install curl
```
Si `curl` no está habilitado, edita `php.ini` y descomenta la línea:

```php
extension=curl
```

## 🚀 3. Levantar un Servidor Local 
✅ Opción 1: Usar el servidor embebido de PHP
```bash
php -S localhost:8000
```
✅ Opción 2: Usar Apache o Nginx
Si usas Apache o Nginx, configura un VirtualHost apuntando al directorio del proyecto.

## 🌍 4. Probar la API
### 🔹 Obtener información de un usuario:

**Ejemplo de solicitud**:  
```bash
curl -X GET "http://localhost:8000/analytics/user?id=1"
```
+ `id`: Define el id del usuario a devolver.
### 🔹 Obtener streams en vivo:  

**Ejemplo de solicitud**: 
```bash
curl -X GET "http://localhost:8000/analytics/streams"
```
### 🔹 Obtener streams mas enriquecidos: 
**Ejemplo de solicitud**: 
```bash
curl -X GET "http://localhost:8000/analytics/streams/enriched?limit=2"
```
+ `limit`(opcional): Define el numero de streams a devolver. El valor predeterminado es 3.
## 🔗 URL de la aplicación web

Puedes acceder a la API en la siguiente URL: [Accede a la API](http://twitchanalytics.com.mialias.net/)
- Usuario: twitch896
- Contraseña: r3lqKhjC
  
💬 No es necesario hacer ninguna configuración manual en el servidor. 

### 🔹 Obtener información de un usuario:
```
http://twitchanalytics.com.mialias.net/analytics/user?id=1
```
+ `id`: Define el id del usuario a devolver. 
### 🔹 Obtener streams en vivo:  
```
http://twitchanalytics.com.mialias.net/analytics/streams
```
### 🔹 Obtener streams mas enriquecidos:
```
http://twitchanalytics.com.mialias.net/analytics/streams/enriched?limit=2
```
+ `limit`(opcional): Define el numero de streams a devolver. El valor predeterminado es 3.

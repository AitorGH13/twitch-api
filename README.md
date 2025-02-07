# Twitch Analytics API

Este proyecto proporciona una API RESTful que permite acceder a información sobre streams y streamers de Twitch, usando datos almacenados en una base de datos MySQL.

## 📂 Estructura del Proyecto

El proyecto contiene los siguientes archivos y carpetas:

- 📄 **.htaccess**: Configura el servidor para que todas las solicitudes se redirijan a `index.php`, habilitando rutas amigables.
- 📄 **index.php**: El punto de entrada principal de la API. Define las rutas y maneja las solicitudes HTTP.
### 📁 Carpeta `src/`
- 📄 **Database.php**: Contiene la clase `Database`, responsable de la conexión a la base de datos y la validación del token de acceso.
- 📄 **StreamController.php**: Contiene la clase `StreamController`, que proporciona métodos para obtener información sobre los streams.
- 📄 **StreamerController.php**: Contiene la clase `StreamerController`, que proporciona un método para obtener información detallada sobre un streamer.
  
## 📌 Guía para Ejecutar la API de Twitch en Local

## 📥 1. Clonar el Repositorio

Primero, debes descargar el código desde GitHub. Abre una terminal y ejecuta:

```bash
git clone https://github.com/AitorGH13/twitchapi.git
```
```bsh
cd twitchapi
```
## 🔧 2. Configurar el Entorno

Necesitas PHP y cURL instalados en tu sistema. Puedes verificarlo ejecutando:

```bash
php -v
php -m | grep curl
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
curl -X GET "http://localhost:8000/analytics/user?id=12345"
```
**Ejemplo de respuesta**:  
```json
{
  "id": 12345,
  "display_name": "StreamerExample",
  "profile_image_url": "https://example.com/profile.jpg",
  "bio": "Gamer, streamer, and all-around cool person."
}
```
### 🔹 Obtener streams en vivo:  

**Ejemplo de solicitud**: 
```bash
curl -X GET "http://localhost:8000/analytics/streams"
```
**Ejemplo de respuesta**:  
```json
[
  {
    "title": "Stream 1",
    "user_id": 12345
  },
  {
    "title": "Stream 2",
    "user_id": 67890
  }
]
```
### 🔹 Obtener streams mas populares: 
 
**Párametros:**
+ `limit`(opcional): Define el numero de streams a devolver. El valor predterminado es 3.

**Ejemplo de solicitud**: 
```bash
curl -X GET "http://localhost:8000/analytics/streams/enriched?limit=5"
```
**Ejemplo de respuesta**:  
```json
[
  {
    "stream_id": 1,
    "title": "Stream 1",
    "viewer_count": 5000,
    "display_name": "StreamerExample",
    "profile_image_url": "https://example.com/profile.jpg"
  },
  {
    "stream_id": 2,
    "title": "Stream 2",
    "viewer_count": 4000,
    "display_name": "AnotherStreamer",
    "profile_image_url": "https://example.com/profile2.jpg"
  }
]
```
### 🔗 URL de la aplicación en vivo

Puedes acceder a la API en la siguiente URL:  
[Accede a la API](http://twitchanalytics.com.mialias.net/)

No es necesario hacer ninguna configuración manual en el servidor o en la base de datos. Solo asegúrate de que la URL esté correctamente configurada en tu servidor web y la API funcionará como se espera.






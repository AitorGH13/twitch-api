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
curl -X GET "http://localhost:8000/analytics/user?id=1" -H "Token: n2rnsruj57culzwz2iznqx6y5jbata" -H "Client-Id: iw4dxrhn2yqaethe9b6uwdbanf3xiw"
```
**Ejemplo de respuesta**:  
```json
{
    "data": [
        {
            "id": "1",
            "login": "elsmurfoz",
            "display_name": "elsmurfoz",
            "type": "",
            "broadcaster_type": "",
            "description": "",
            "profile_image_url": "https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png",
            "offline_image_url": "",
            "view_count": 0,
            "created_at": "2007-05-22T10:37:47Z"
        }
    ]
}
```
### 🔹 Obtener streams en vivo:  

**Ejemplo de solicitud**: 
```bash
curl -X GET "http://localhost:8000/analytics/streams" -H "Token: n2rnsruj57culzwz2iznqx6y5jbata" -H "Client-Id: iw4dxrhn2yqaethe9b6uwdbanf3xiw"
```
**Ejemplo de respuesta**:  
```json
[
    {
        "id": "316115893245",
        "user_id": "31239503",
        "user_login": "eslcs",
        "user_name": "ESLCS",
        "game_id": "32399",
        "game_name": "Counter-Strike",
        "type": "live",
        "title": "LIVE: The Mongolz vs. Eternal Fire - IEM Katowice 2025",
        "viewer_count": 77501,
        "started_at": "2025-02-07T13:15:24Z",
        "language": "en",
        "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_eslcs-{width}x{height}.jpg",
        "tag_ids": [],
        "tags": [
            "English"
        ],
        "is_mature": false
    }
]
```
### 🔹 Obtener streams mas enriquecidos: 
 
**Párametros:**
+ `limit`(opcional): Define el numero de streams a devolver. El valor predterminado es 3.

**Ejemplo de solicitud**: 
```bash
curl -X GET "http://localhost:8000/analytics/streams/enriched?limit=2" -H "Token: n2rnsruj57culzwz2iznqx6y5jbata" -H "Client-Id: iw4dxrhn2yqaethe9b6uwdbanf3xiw"
```
**Ejemplo de respuesta**:  
```json
[
    {
        "stream_id": "316115893245",
        "title": "LIVE: The Mongolz vs. Eternal Fire - IEM Katowice 2025",
        "viewer_count": 77501,
        "display_name": "ESLCS",
        "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/c1ecbcd5-b8b6-4e0c-9d5f-e01d610aa97d-profile_image-300x300.png"
    },
    {
        "stream_id": "316120598013",
        "title": "🔴ETERNAL FIRE vs MONGOLZ | IEM KATOWICE 2025🔴",
        "viewer_count": 50090,
        "display_name": "ohnePixel",
        "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/5742b015-e6ed-4f7c-a1dd-87cd88fe1eb9-profile_image-300x300.png"
    }
]
```
## 🔗 URL de la aplicación web

Puedes acceder a la API en la siguiente URL: [Accede a la API](http://twitchanalytics.com.mialias.net/)

💬 No es necesario hacer ninguna configuración manual en el servidor. 






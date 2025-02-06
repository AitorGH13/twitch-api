# Twitch Analytics API

Este proyecto proporciona una API RESTful que permite acceder a información sobre streams y streamers de Twitch, usando datos almacenados en una base de datos MySQL.

## Estructura del Proyecto

El proyecto contiene los siguientes archivos y carpetas:

- **.htaccess**: Configura el servidor para que todas las solicitudes se redirijan a `index.php`, habilitando rutas amigables.
- **index.php**: El punto de entrada principal de la API. Define las rutas y maneja las solicitudes HTTP.
### Carpeta `src/`
- **Database.php**: Contiene la clase `Database`, responsable de la conexión a la base de datos y la validación del token de acceso.
- **StreamController.php**: Contiene la clase `StreamController`, que proporciona métodos para obtener información sobre los streams.
- **StreamerController.php**: Contiene la clase `StreamerController`, que proporciona un método para obtener información detallada sobre un streamer.

## Recursos utilizados
- phpMyAdmin para crear [nuestraBaseDeDatos](https://phpmyadmin.mi-alojamiento.com/?d=twitchanalytics.com")
  
## Instalación y Ejecución

### Ejecución en la Web

La API está configurada para ejecutarse directamente en la web, sin necesidad de hacer configuraciones adicionales. El proceso es completamente autónomo:

1. **Base de datos**: La base de datos `twitchanalytics` ya está creada y configurada con las credenciales necesarias. No es necesario realizar configuraciones adicionales en la base de datos.
2. **API**: La API está lista para usarse. Simplemente accede a la URL de la aplicación web donde está alojada y la API se ejecutará automáticamente.
3. **Servidor web**: El servidor web (como Apache) está configurado para redirigir automáticamente todas las solicitudes a `index.php`, por lo que no se requiere ninguna acción extra.

### URL de la aplicación en vivo

Puedes acceder a la API en la siguiente URL:  
[Accede a la API](http://twitchanalytics.com.mialias.net/)

No es necesario hacer ninguna configuración manual en el servidor o en la base de datos. Solo asegúrate de que la URL esté correctamente configurada en tu servidor web y la API funcionará como se espera.

## Uso

La API proporciona tres rutas principales:

### 1. **Obtener información de un streamer**  
**Ruta**: `/analytics/user?id=<id>`  
**Método**: `GET`  
**Autenticación**: Se requiere un token de acceso de Twitch en el encabezado `Authorization` (formato `Bearer <token>`).  
**Respuesta**: Información del streamer en formato JSON. 

**Ejemplo de solicitud**:  
```bash
GET /analytics/user?id=12345
Authorization: Bearer <tu_token>
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
### 2. **Obtener información de los streams en vivo**  
**Ruta**: `/analytics/streams`  
**Método**: `GET`  
**Autenticación**: No es necesaria.  
**Respuesta**: Una lista de streams en vivo en formato JSON.  

**Ejemplo de solicitud**: 
```bash
GET /analytics/streams
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
### 3. **Obtener los streams mas populares**  
**Ruta**: `/analytics/streams/enriched?limit=<limit>`  
**Método**: `GET`  
**Autenticación**: No es necesaria.  
**Párametros:**
+ `limit`(opcional): Define el numero de streams a devolver. El valor predterminado es 3.
  
**Respuesta**: Una lista de streams en vivo en formato JSON.

**Ejemplo de solicitud**: 
```bash
GET /analytics/streams/enriched?limit=5
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







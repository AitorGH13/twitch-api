# Twitch Analytics API

Este proyecto proporciona una API RESTful que permite acceder a información sobre streams y streamers de Twitch, usando datos almacenados en una base de datos MySQL.

**API en vivo:** [Utiliza nuestra API](http://twitchanalytics.com.mialias.net/)
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



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
- 🐘 **AuthController.php**: Gestiona la creación y validación de claves de API y tokens de acceso para usuarios a través de su correo electrónico.
- 🐘 **StreamController.php**: Obtener información sobre los streams.
- 🐘 **StreamerController.php**: Obtener información detallada sobre un streamer.
- 🐘 **VideoController.php**: Obtener y almacenar información sobre los tres juegos más populares y sus videos más vistos.
  
# 📌 Guía para ejecutarlo en local
## 📋 Requisitos previos
- **Servidor Web Local:** Necesitas tener instalado un servidor web local como [XAMPP](https://www.apachefriends.org/es/index.html), [MAMP](https://www.mamp.info/en/downloads/), o [WAMP](https://www.wampserver.com/en/). Asegúrate de tener Apache y MySQL funcionando.
- **PHP:** Asegúrate de tener PHP instalado en tu entorno local. Puedes comprobarlo ejecutando `php -v` en tu terminal.
- **Base de Datos MySQL:** Este proyecto requiere MySQL. Asegúrate de tener acceso a un servidor MySQL local.
- **Git:** Es necesario tener Git instalado para clonar el repositorio.
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
## 🗄️ 3. Crear la Base de Datos
Dentro del repositorio clonado, encontrarás el archivo database.sql, que contiene la estructura de la base de datos. Sigue estos pasos para importarlo en tu servidor MySQL:
1. Abre [phpMyAdmin](https://www.phpmyadmin.net/).
2. Crea una nueva base de datos. En el panel izquierdo, haz clic en **"Nueva"** e ingresa el nombre de la base de datos (por ejemplo, `twitchanalytics`).
3. Selecciona la base de datos recién creada y haz clic en la pestaña **"Importar"**.
4. En Archivo a importar, selecciona el archivo `database.sql` que se encuentra en el repositorio clonado.
5. Haz clic en **"Ejecutar"** para cargar la base de datos.
   
Una vez que la importación se haya completado, deberías ver las tablas y los datos necesarios en la base de datos.

## 🔧 Configuración de `database.php`
Para conectar la API con la base de datos correctamente, debes ajustar las credenciales de conexión en el archivo database.php con los detalles de tu entorno local.
1. Abre el archivo database.php.
2. Modifica las siguientes líneas según tu configuración:
private static $conn;
```php
$host = "localhost"; // Dirección del servidor de base de datos
$dbname = "twitchanalytics"; // Nombre de la base de datos
$username = "root"; // Nombre de usuario de la base de datos
$password = ""; // Contraseña de la base de datos
```
### Parámetros a modificar:
- **$host:** Si tu servidor MySQL está en localhost, no necesitarás cambiar este valor.
- **$dbname:** Debe ser el nombre de la base de datos que importaste desde `database.sql`. Si la base de datos se llama `twitchanalytics`, asegúrate de que coincida.
- **$username:** Es el nombre de usuario de tu base de datos MySQL. En muchos casos, el nombre de usuario predeterminado en entornos locales es `root`. Si has configurado otro usuario, cámbialo aquí.
- **$password:** Es la contraseña de tu base de datos MySQL. Si estás usando `root` como usuario, es común que no haya contraseña en entornos locales. Si no tienes contraseña, deja este valor vacío (`""`).

## 🚀 4. Levantar un Servidor Local 
✅ Opción 1: Usar el servidor embebido de PHP
```bash
php -S localhost:8000
```
✅ Opción 2: Usar Apache o Nginx
Si usas Apache o Nginx, configura un VirtualHost apuntando al directorio del proyecto.


## 🌍 5. Probar la API mediante Curl
### 🔹 Registrar usuario:

```bash
curl -X POST "http://54.219.250.68/register" \
  -d '{"email": "tu_correo@example.com"}'
```
### 🔹 Obtener token:

```bash
curl -X POST "http://54.219.250.68/token" \
  -d '{"email": "tu_correo@example.com", "api_key": "tu_clave"}'
```
### 🔹 Obtener Tops of the tops:

```bash
curl -X GET "http://54.219.250.68/analytics/topsofthetops" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### 🔹 Obtener información de un usuario:

**Ejemplo de solicitud**:  
```bash
curl -X GET "http://54.219.250.68/analytics/user?id=1" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
+ `id`: Define el id del usuario a devolver.
### 🔹 Obtener streams en vivo:  

**Ejemplo de solicitud**: 
```bash
curl -X GET "http://54.219.250.68/analytics/streams" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### 🔹 Obtener streams mas enriquecidos: 
**Ejemplo de solicitud**: 
```bash
curl -X GET "http://54.219.250.68/analytics/streams/enriched?limit=2" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
+ `limit`(opcional): Define el numero de streams a devolver. El valor predeterminado es 3.
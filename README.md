<div align="center">
  <img src="https://github.com/user-attachments/assets/0cbd9c53-4b98-40dd-8fbe-529792b9bad9" alt="Logo del proyecto" width="300">
</div>

# Requisitos
- [Docker](https://www.docker.com/) instalado y funcionando.
# Instalación
1. Clona y accede al repositorio:
```bash
git clone https://github.com/AitorGH13/twitchapi.git ; cd twitchapi
```
2. Instala las dependencias mediante composer:
```bash
composer install
```
3. Copia el archivo `.env.example` a `.env`:

```bash
cp .env.example .env
```
> [!IMPORTANT]
> Asegúrate de completar correctamente las variables `TWITCH_CLIENT_ID` y `TWITCH_CLIENT_SECRET` en el archivo `.env` para que la autenticación con Twitch funcione correctamente.
## Uso
- Para **construir y levantar** los contenedores desde cero, ejecuta el siguiente comando:
```bash
make build
```
- Para **levantar** los contenedores ya existentes, ejecuta el siguiente comando:
```bash
make up
```
- Para **detener** los contenedores sin eliminarlos, ejecuta el siguiente comando:
```bash
make stop
```
- Para **eliminar** contenedores, volúmenes y recursos huérfanos, ejecuta el siguiente comando:
```bash
make clean
```
- Para **ejecutar los tests** con PHPUnit, ejecuta el siguiente comando:
```bash
make test
```
- Para **abrir una terminal** dentro del contenedor app, ejecuta el siguiente comando:
```bash
make shell
```
- Para **aplicar las migraciones** de la base de datos, ejecuta el siguiente comando:
```bash
make migrate
```
## Endpoints 
> [!IMPORTANT]
> Antes de ejecutar cualquier endpoint, asegúrate de haber aplicado las migraciones de la base de datos.
### · Registrar usuario:
```bash
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{"email": "tu_correo@example.com"}'
```
### · Obtener token:

```bash
curl -X POST http://localhost:8000/token \
  -H "Content-Type: application/json" \
  -d '{"email": "tu_correo@example.com", "api_key": "tu_clave"}'
```
### · Obtener Tops of the tops:
```bash
curl -X GET "http://localhost:8000/analytics/topsofthetops" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Obtener información de un usuario:
```bash
curl -X GET "http://localhost:8000/analytics/user?id=1" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Obtener streams en vivo:
```bash
curl -X GET "http://localhost:8000/analytics/streams" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Obtener streams mas enriquecidos:
```bash
curl -X GET "http://localhost:8000/analytics/streams/enriched?limit=3" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
> [!NOTE]
> Si deseas probar la API desplegada en un servidor remoto, simplemente reemplaza `localhost:8000` por `54.219.250.68`.

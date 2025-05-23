<div align="center">
  <img src="https://github.com/user-attachments/assets/81c41d22-b9d8-4fcc-ae5b-fd627b54edae" alt="Logo del proyecto" width="300">
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
> [!NOTE]
> TWITCH_CLIENT_ID=tu_client_id_aquí  
> TWITCH_CLIENT_SECRET=tu_client_secret_aquí
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
> [!NOTE]
> Las migraciones debén estar aplicadas.
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
## Prueba remota sin contenedor
### · Registrar usuario:
```bash
curl -X POST "http://54.219.250.68/register" \
  -d '{"email": "tu_correo@example.com"}'
```
### · Obtener token:
```bash
curl -X POST "http://54.219.250.68/token" \
  -d '{"email": "tu_correo@example.com", "api_key": "tu_clave"}'
```
### · Obtener Tops of the tops:
```bash
curl -X GET "http://54.219.250.68/analytics/topsofthetops" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Obtener información de un usuario:
```bash
curl -X GET "http://54.219.250.68/analytics/user?id=1" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Obtener streams en vivo:
```bash
curl -X GET "http://54.219.250.68/analytics/streams" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Obtener streams mas enriquecidos:
```bash
curl -X GET "http://54.219.250.68/analytics/streams/enriched?limit=3" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

<div align="center">
  <img src="https://github.com/user-attachments/assets/0cbd9c53-4b98-40dd-8fbe-529792b9bad9" alt="Logo del proyecto" width="300">
</div>

# Requisitos
- [Docker](https://www.docker.com/) instalado y funcionando.
# Instalación
1. Clona y accede al repositorio:
```bash
git clone https://github.com/AitorGH13/twitch-api.git ; cd twitch-api
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
> Completa correctamente las variables `TWITCH_CLIENT_ID` y `TWITCH_CLIENT_SECRET` en el archivo `.env`.
# Uso
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
- Para **ejecutar los tests unitarios** con PHPUnit, ejecuta el siguiente comando:
```bash
make unit
```
- Para **ejecutar los tests de integración** con PHPUnit, ejecuta el siguiente comando:
```bash
make integration
```
- Para **ejecutar todos los tests** con PHPUnit, ejecuta el siguiente comando:
```bash
make tests
```
- Para **abrir una terminal** dentro del contenedor app, ejecuta el siguiente comando:
```bash
make shell
```
- Para **aplicar las migraciones** de la base de datos, ejecuta el siguiente comando:
```bash
make migrate
```
# Endpoints 
> [!IMPORTANT]
> Antes de ejecutar cualquier endpoint, asegúrate de haber aplicado las migraciones de la base de datos.
### · Registro de Usuarios:
```bash
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{"email": "tu_correo@example.com"}'
```
### · Obtención de Token de Sesión:

```bash
curl -X POST http://localhost:8000/token \
  -H "Content-Type: application/json" \
  -d '{"email": "tu_correo@example.com", "api_key": "tu_clave"}'
```
### · Consultar "Top Of The Tops":
```bash
curl -X GET "http://localhost:8000/analytics/topsofthetops" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Consultar Información de un Streamer:
```bash
curl -X GET "http://localhost:8000/analytics/user?id=1" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Consultar Streams en vivo:
```bash
curl -X GET "http://localhost:8000/analytics/streams" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```
### · Consultar “Top Streams Enriquecidos”:
```bash
curl -X GET "http://localhost:8000/analytics/streams/enriched?limit=3" \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

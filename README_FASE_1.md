# Sistema FESIRMES - Fase 1

Esta fase contiene solamente:

- Login funcional.
- Roles: Administrador, Secretaría y Consulta.
- Dashboard basico responsive.
- CRUD de afiliados.
- Migraciones para usuarios, sesiones, cache, colas, afiliados y auditoria.
- Middleware de permisos por rol.
- Auditoria basica de login, logout, creacion, edicion y baja de afiliados.
- Validacion de C.I. unico.
- Estados de afiliado: activo, baja, suspendido y observado.

No incluye aportes, asistencia, QR, credenciales ni PDF.

## Permisos

- Administrador: puede crear, ver, editar, cambiar C.I. y marcar como baja.
- Secretaría: puede crear, ver y editar, pero no puede modificar C.I.
- Consulta: solo puede ver dashboard y afiliados.

## Instalacion local

1. Instalar PHP 8.2 o superior, Composer, Node.js y MySQL.
2. Crear una base MySQL llamada `fesirmes`.
3. Copiar `.env.example` a `.env`.
4. Revisar en `.env` los datos de MySQL.
5. Ejecutar:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Usuarios de prueba

Todos usan la contrasena `password`.

- admin@fesirmes.local
- secretaria@fesirmes.local
- consulta@fesirmes.local

## AWS EC2

Para EC2 se recomienda:

- Ubuntu LTS.
- Nginx o Apache apuntando a la carpeta `public`.
- PHP 8.2+ con extensiones `mbstring`, `xml`, `curl`, `zip`, `mysql`, `bcmath`.
- MySQL en la misma instancia o Amazon RDS.
- `APP_ENV=production`, `APP_DEBUG=false`.
- Ejecutar `php artisan config:cache`, `route:cache` y `view:cache` despues de configurar `.env`.

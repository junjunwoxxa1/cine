FROM webdevops/php-nginx:8.3

# Configurar la ruta pública para Nginx
ENV WEB_DOCUMENT_ROOT=/app/public

# Variables de entorno para producción
ENV APP_ENV=production
ENV APP_DEBUG=false

WORKDIR /app

# Copiar todos los archivos del proyecto al contenedor
COPY . .

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Crear el archivo de base de datos SQLite y ejecutar las migraciones
RUN touch database/database.sqlite
RUN php artisan migrate --force

# Dar los permisos necesarios a las carpetas de Laravel y a la base de datos
RUN chown -R application:application /app/storage /app/bootstrap/cache /app/database
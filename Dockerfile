# Define the base image
FROM teguh02/laravel-filament:latest

# Set user to root
USER root

# Change the working directory
WORKDIR /var/www

# Remove the all files in the /var/www/html directory
RUN rm -rf /var/www/html/*

# Copy laravel public directory to the /var/www/html directory
COPY ./public /var/www/html

# Copy the project files to the /var/www directory
COPY . /var/www

# Install the project dependencies
RUN composer install 
RUN npm install

# Build the vite
RUN npm run build

# Change the directory permission
RUN chmod -R 777 /var/www

# If you want to change the timezone of the container to UTC
# RUN sed -i 's/;date.timezone =/date.timezone = UTC/g' /etc/php/8.3/fpm/php.ini
# RUN sed -i 's/;date.timezone =/date.timezone = UTC/g' /etc/php/8.3/cli/php.ini

# If you want to display the error message
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/8.3/fpm/php.ini
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/8.3/cli/php.ini
RUN sed -i 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/g' /etc/php/8.3/fpm/php.ini

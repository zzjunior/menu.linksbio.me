# Dockerfile para simular ambiente de produção PHP + Apache
# Use a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instale dependências do sistema necessárias para mbstring
RUN apt-get update && apt-get install -y libonig-dev \
	&& docker-php-ext-install pdo pdo_mysql mbstring \
	&& apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilite o mod_rewrite do Apache
RUN a2enmod rewrite


# Copie apenas as pastas e arquivos necessários para o diretório padrão do Apache
COPY app/ /var/www/html/app/
COPY public/ /var/www/html/public/
COPY config/ /var/www/html/config/
COPY routes/ /var/www/html/routes/
COPY views/ /var/www/html/views/
COPY vendor/ /var/www/html/vendor/
COPY composer.json /var/www/html/
COPY composer.lock /var/www/html/

# Defina permissões para o diretório
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta padrão do Apache
EXPOSE 80

# Para simular o ambiente, basta rodar 'docker build -t meuapp .' e 'docker run -p 8080:80 meuapp'.
# Este Dockerfile pode ficar no repositório, mas não será enviado para produção real, apenas para testes locais.
# Usa una imagen oficial de Node.js 21.7.1
FROM node:21.7.1

# Establece el directorio de trabajo
WORKDIR /app

# Instala pnpm
RUN npm install -g pnpm

# Copia el archivo package.json y pnpm-lock.yaml
COPY package.json pnpm-lock.yaml ./

# Instala dependencias utilizando pnpm
RUN pnpm install

# Copia el resto del proyecto al contenedor
COPY . .

# Construye el proyecto
RUN pnpm run build

# Expone el puerto 4321 (o el puerto que uses en desarrollo)
EXPOSE 4321

# Establece el comando por defecto para ejecutar la aplicación
CMD ["pnpm", "run", "start"]

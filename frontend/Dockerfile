FROM node:20
WORKDIR /app

# Copia solo package.json para instalar dependencias primero
COPY package*.json ./
RUN npm install

# Copia todo el frontend
COPY . .

EXPOSE 3000
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

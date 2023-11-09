FROM node:lts-alpine as build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM node:lts-alpine as production-stage
WORKDIR /app
COPY package*.json ./
RUN npm install --only=production
COPY --from=build-stage /app/dist ./dist
EXPOSE 8000
CMD ["node", "dist/index.js"]

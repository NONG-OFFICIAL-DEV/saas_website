# Stage 1: Build
FROM node:20-alpine AS build-stage
WORKDIR /app

COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Run the Nitro Node server
# Nitro's node-server preset bundles everything the server needs into
# .output/server (including its own node_modules) — no separate npm
# install needed in this stage, just Node itself.
FROM node:20-alpine
WORKDIR /app

ENV NODE_ENV=production
# Reverse-proxied by the shared external Caddy instance at
# smart-store-website:80 (see root docker-compose.yml) — the old
# static-file container also listened on 80, so this keeps that
# routing working unchanged rather than requiring a reverse-proxy change.
ENV PORT=80
ENV HOST=0.0.0.0

COPY --from=build-stage /app/.output /app/.output

EXPOSE 80
CMD ["node", ".output/server/index.mjs"]

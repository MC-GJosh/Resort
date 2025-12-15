# Dockerfile
FROM node:18-alpine

# update and install dependencies
RUN apk update && apk upgrade && apk add git

# create destination directory
RUN mkdir -p /usr/src/nuxt-app
WORKDIR /usr/src/nuxt-app

# copy the app, note .dockerignore
COPY . /usr/src/nuxt-app/

# install dependencies
RUN npm install

# build the app
RUN npm run build

# expose 3000 on container
EXPOSE 3000

# set app host and port and start the app
ENV NUXT_HOST=0.0.0.0
ENV NUXT_PORT=3000

# start the app
CMD [ "node", ".output/server/index.mjs" ]

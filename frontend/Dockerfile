FROM node:20-alpine

WORKDIR /var/www

COPY package*.json ./

COPY . .

RUN chmod +x docker/entrypoint.sh

ENTRYPOINT ["docker/entrypoint.sh"]

CMD ["npm","start"]
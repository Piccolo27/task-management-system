#!/bin/sh

if [ ! -f ".env" ]; then
    echo "Creating env file"
    cp .env.example .env
fi

if [ ! "$(ls -A /usr/src/app/node_modules)" ]; then
  echo "Node modules not installed. Installing..."
  npm install
fi

echo "Starting Nuxt App..."
exec "$@"
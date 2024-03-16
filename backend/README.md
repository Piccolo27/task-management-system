- composer update
- cp .env.example .env
- php artisan key:generate
- php artisan migrate --seed
- php artisan storage:link
- php artisan jwt:secret

Change these value in env file
```
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=database
```

- Setup pusher account and smtp mail

## Serve
- php artisan serve 
- php artisan queue:work

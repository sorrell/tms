![lptms](https://github.com/user-attachments/assets/7cff521c-d128-495b-b7bd-5e555af98c0b)

[![Deploy to DO](https://www.deploytodo.com/do-btn-blue.svg)](https://cloud.digitalocean.com/apps/new?repo=https://github.com/loadpartner/tms/tree/main)

The Open Source Transportation Management System for Freight Brokers

#### 🌐 [tms.loadpartner.io](https://tms.loadpartner.io)

## Get involved
🗨️ [Discord Community](https://tms.loadpartner.io/links/discord) 

📰 [Subscribe to the newsletter](https://tms.loadpartner.io/links/newsletter)

## Tech Stack
[Laravel](http://laravel.com/) + [Inertia.js](https://inertiajs.com/) + [React](https://react.dev/)

## Development
A local environment can be setup quickly using [Laravel Sail](https://laravel.com/docs/master/sail) and our docker configurations.


### Fresh Setup
Standing up the development environment after cloning the repository should follow these steps:

#### Setup [Laravel Sail](https://laravel.com/docs/11.x/sail)
``` bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

Can't find `sail` in your path? Add to your bashrc/zshrc
``` bash
export PATH=./vendor/bin:$PATH
```
  
#### Copy `env` file
``` bash
cp .env.example .env
```

#### Up sail
``` bash
sail up -d
```

#### Setup database 
``` bash
sail artisan migrate
```

#### Install npm modules
``` bash
sail npm install
```

#### Generate APP_KEY
``` bash
sail artisan key:generate
```

#### Run Vite
``` bash
sail npm run dev
```

#### [OPTIONAL] Setup Test Data
``` bash
sail artisan dev:refresh
```

### More Development Questions?
Checkout [DEVELOPMENT.md](./DEVELOPMENT.md) for some frequently ask questions.

If you still have questions, please join our discord for help!

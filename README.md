<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Запуск с помощью Laravel Sail
```shell
# Клонирование проекта
mkdir warehouse-accounting
cd warehouse-accounting

git init
git remote add origin https://github.com/eldargasanov1/warehouse-accounting.git
git pull origin master

# Установка и запуск Laravel Sail
composer require laravel/sail --dev
cp .env.example .env
php artisan sail:install -> Enter
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan make:main-user -> Скопируйте токен из терминала
./vendor/bin/sail artisan db:seed

# Приложение запущено по адресу http://127.0.0.1/

# Готово!
```
## Routes
Перед выполнением запроса необходимо в **_headers_** указать полученный **_токен авторизации_**.
### Просмотр списка складов:
```ts
async function getWarehouses() {
    const request = new Request("/api/warehouses", {
        method: "GET",
        headers: {
            'Authorization': 'Bearer <token>'
        },
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Просмотр списка товаров с их остатками по складам:
```ts
async function getProducts() {
    const request = new Request("/api/products", {
        method: "GET",
        headers: {
            'Authorization': 'Bearer <token>'
        },
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Просмотр истории движения товаров:
```ts
interface Filter {
    filter?: {
        warehouse_id?: number[],
        product_id?: number[],
        created_at?: string
    },
    pagination?: {
        page: number,
        perPage: number
    }
}
// Пример фильтра
const filter: Filter = {
    filter: {
        warehouse_id: [1, 2, 3],
        product_id: [1, 2, 3],
        created_at: '2025-05-06'
    },
    pagination: {
        page: 1,
        perPage: 15
    }
}

async function getHistories() {
    const request = new Request("/api/histories", {
        method: "GET",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(filter)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Просмотр списка товаров:
```ts
interface Filter {
    filter?: {
        id?: number[],
        customer?: string,
        warehouse_id?: number[],
        status?: 'active'|'completed'|'cancelled',
        completed_at?: null|string
    },
    pagination?: {
        page: number,
        perPage: number
    }
}
// Пример фильтра
const filter: Filter = {
    filter: {
        id: [1, 2, 3],
        customer: 'Prof',
        warehouse_id: [1, 2, 3],
        status: 'active',
        completed_at: '2025-05-06'
    },
    pagination: {
        page: 1,
        perPage: 15
    }
}

async function getOrders() {
    const request = new Request("/api/orders", {
        method: "GET",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(filter)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Создание заказа:
```ts
interface Order {
    customer: string,
    warehouse_id: number,
    status?: 'active'|'completed'|'cancelled',
    products: [
        {
            id: number,
            count: number
        }
    ]
}
const order: Order = {
    customer: 'Name',
    warehouse_id: 1,
    status: "active",
    products: [
        {
            id: 1,
            count: 5
        },
        {
            id: 2,
            count: 10
        }
    ]
};

async function createOrder() {
    const request = new Request("/api/orders", {
        method: "POST",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(order)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Изменение заказа:
```ts
interface Order {
    customer?: string,
    products?: [
        {
            id: number,
            count: number
        }
    ]
}
const order: Order = {
    customer: 'Name',
    products: [
        {
            id: 1,
            count: 5
        },
        {
            id: 2,
            count: 10
        }
    ]
};

async function updateOrder() {
    const request = new Request("/api/orders/{id}", {
        method: "PATCH",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(order)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Завершить заказ:
```ts
interface Order {
    id: number
}
const order: Order = {
    id: 1
};

async function completeOrder() {
    const request = new Request("/api/orders/complete", {
        method: "POST",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(order)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Отменить заказ:
```ts
interface Order {
    id: number
}
const order: Order = {
    id: 1
};

async function cancelOrder() {
    const request = new Request("/api/orders/cancel", {
        method: "POST",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(order)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```
### Возобновить заказ:
```ts
interface Order {
    id: number
}
const order: Order = {
    id: 1
};

async function continueOrder() {
    const request = new Request("/api/orders/continue", {
        method: "POST",
        headers: {
            'Authorization': 'Bearer <token>'
        },
        body: JSON.stringify(order)
    });

    const response = await fetch(request);
    const result = await response.json()
    console.log(result);
}
```

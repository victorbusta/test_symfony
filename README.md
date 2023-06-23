# Symfony test

'Symfony test' is a web application that allows users to browse and filter car listings. It provides a user-friendly interface to search for cars based on categories and names. The application utilizes Symfony framework for backend development and integrates with a car database.

## Features

- Browse a list of cars with details such as ID, name, category, number of seats, number of doors, and cost.
- Filter cars by category and name using the search functionality.
- Pagination support for efficient navigation through the car listings.
- Integration with Open-Meteo API to display the current temperature on the main page.
- Responsive design for optimal viewing on different devices.

## Technology used:
- Symfony 6
- docker
- PostgreSQL

## Installation

1. Clone the repository:

```
git clone https://github.com/victorbusta/test_symfony
```

2. Install the dependencies using Composer and Npm:

```
composer install
npm install && npm run build

```

3. Run de development database 

```
docker compose up --build -d
```

4. Create schema and load fixtures

```
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load
```

5. Start the development server:
```
symfony serve
```

6. Access the application in your web browser at 
- http://localhost:8000 (shop page)
- http://localhost:8000/resource (admin page)




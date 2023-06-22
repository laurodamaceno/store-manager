# store-manager
This is just a repository of my Store Manager project, using SQL Server for DB, backend in PHP and Frontend with Next.JS

# ABOUT THE PROJECT

As proposed, I am using MSSQL for data management. I have left a backup of the database in the `./db` path named `store_manager`. Before importing, make sure to include the `.bak` extension.

For the construction of the Rest API, I have chosen to use only PHP with PDO and prioritized the minimal possible security. On my local server, I am using the connector with Microsoft ODBC 18, and the API is located in the `./backend` directory.

## IMPORTANT

Remember to update the connection file with the DB, located at `./backend/src/` with the name `connection.php`.

```php
private $serverName = "localhost";
private $database = "store_manager";
private $uid = "SA";
private $pwd = "your-pass-word-here";

Please provide the details of your local server for it to function correctly.

## ABOUT THE BACKEND INITIALIZATION

After cloning the project to your local machine and updating the connection details, start the backend server from the ./backend directory using the command php -S localhost:8080. Then, you can test the API routes using software such as Insomnia or Postman, or simply start the frontend and witness the magic.


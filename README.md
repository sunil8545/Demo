## About Project

This project is only for demonstration purposes.
We are developing a rest API for customers, products, and orders in this project.

## Project Setup

- Run the php artisan migrate command to create the table structure.
- Execute the php artisan customer:import command to add customers from a url file to the customers table.
- Use the php artisan product:import command to add products from a url file to the products table.
- You can create multiple payment gateways in the app/Services/Payment folder and use the PaymentGateway class to implement them.
- Change the payment gateway in the AppServiceProvider file and bind PaymentGateway to your Payment Gateway.
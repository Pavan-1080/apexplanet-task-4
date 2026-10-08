# Inventory Management System

A small full-stack Inventory Management System developed as part of the ApexPlanet Full Stack Web Development Internship.

## Project Overview

The Inventory Management System allows users to securely log in and view inventory products.

Administrators have additional permissions to manage products, monitor inventory statistics, and identify low-stock products.

## Technologies Used

- HTML5
- CSS3
- Bootstrap 5
- PHP
- MySQL
- JavaScript
- XAMPP
- Git & GitHub

## Main Features

### Authentication

- User registration
- User login
- Secure password hashing
- Session-based authentication
- Logout functionality
- User and Admin roles

### Product Management

Administrators can:

- Add products
- View products
- Edit products
- Delete products
- Search products
- Filter products by category
- Navigate products using pagination

### Admin Panel

The Admin Panel provides:

- Total product count
- Total inventory quantity
- Total inventory value
- Total category count
- Low-stock product monitoring
- Quick access to product management

### Database

The system uses MySQL with two main tables:

- `users`
- `products`

The `products` table contains a foreign key relationship with the `users` table through `created_by`.

Indexes are used for product names, categories, and creator IDs to improve database queries.

## User Roles

### User

- Login and logout
- View dashboard
- View products
- Search products
- Filter products
- Use pagination

### Admin

- All user permissions
- Add products
- Edit products
- Delete products
- Access Admin Panel
- View inventory statistics
- Monitor low-stock products

## Project Structure

```text
apexplanet-task-4/
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── admin.php
├── products.php
├── add_product.php
├── edit_product.php
├── delete_product.php
├── db.php
├── style.css
├── README.md
├── WIREFRAME.md
└── uploads/
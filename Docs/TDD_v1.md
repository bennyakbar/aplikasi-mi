# Technical Design Document (TDD) v1.0

## 1. Architecture
Monolithic Laravel MVC

Client -> Web Server -> Laravel -> Database

## 2. Tech Stack
- PHP 8.2
- Laravel 10
- PostgreSQL
- Blade + Bootstrap

## 3. Folder Structure
app/Controllers  
app/Models  
app/Services  

## 4. Transaction Handling
Gunakan DB Transaction

## 5. Security
RBAC, CSRF, Hash Password

## 6. Deployment
Git clone -> composer install -> migrate -> seed

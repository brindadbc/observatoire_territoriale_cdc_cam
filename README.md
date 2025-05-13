<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

<p align="center">
    <a href="https://github.com/laravel/framework/actions">
        <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
    </a>
</p>

# **E-Community Management System** 🎓  

A role-based community management platform built with Laravel, featuring Admin and Observers dashboards.

## **Table of Contents**
1. [Features](#features)
2. [Prerequisites](#prerequisites)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Testing the Application](#testing-the-application)
6. [Usage](#usage)
7. [License](#license)

---

## **Features**
✅ Role-based authentication (Admin, Observer)  
✅ Dashboard for each user type  
✅ Profile management  
✅ Secure authentication (Login, Logout, Registration)  

---

## **Prerequisites**
Before setting up the project, make sure you have the following installed:

- **PHP (>= 8.0)**  
- **Composer**  
- **MySQL / MariaDB**  
- **Node.js** (for frontend assets)  
- **Git**

---

## **Installation**

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/brindadbc/observatoire_territoriale_cdc_cam  
```
```bash
cd observatoire_territoriale_cdc_cam  
```
### 2️⃣ Install PHP Dependencies
```bash
composer install 
``` 

### 3️⃣ Install Frontend Dependencies
```bash
npm install
```  

---

## **Configuration**

### 1️⃣ Create `.env` File
Copy the example environment file:
```bash
cp .env.example .env  
```

### 2️⃣ Generate Application Key
```bash
php artisan key:generate  
```

### 3️⃣ Set Up Database
Update your `.env` file with the correct database credentials:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cdc_territoriale
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ Migrate & Seed Database
```bash
php artisan migrate --seed  
```

This will create a default **Admin** user: 

- **Email:** `admin@cdc.com`  
- **Password:** `password`  


---

## **Testing the Application**

### 1️⃣ Start the Development Server
```bash
php artisan serve  
```

The application should now be accessible at `http://127.0.0.1:8000`.

### 2️⃣ Access the Application
- **Admin Login:**  

  - Email: `admin@cdc.com`  
  - Password: `password`  


- **Observers** Register a new user or seed additional roles.

---

## **Usage**
- **Admin** → Manages Communes, Statiques, Debts, etc  
- **Observer** → Checks statistiques & others  

---

## **License**
This project is open-source and available under the [MIT License](LICENSE).

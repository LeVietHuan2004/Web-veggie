# 🌱 Veggie - Vegetable E-commerce Website (Laravel 11)

Veggie is an e-commerce website for selling vegetables, built with Laravel 11.
It includes product management, shopping cart, checkout, PayPal integration, wishlist, product reviews, AI chatbot (Gemini API), product recommendation system, and natural language processing (NLTK) for search enhancement.

---

## 🚀 Requirements

-   PHP >= 8.2
-   Composer >= 2
-   MySQL / MariaDB
-   Node.js & NPM (only required if you use frontend build, not mandatory for this version)

---

## ⚙️ Installation

### 1. Clone the project

````bash
git clone https://github.com/dienakdz/veggie.git
cd veggie
````
### 2. Create environment file

````bash
cp .env.example .env
````
## 3. Install dependencies

````bash
composer install
````
## 4. Generate application key

```bash
php artisan key:generate
````
## 5. Run migrations & seeders

````bash
php artisan migrate --seed

````
## 6. Link storage

````bash
php artisan storage:link
````
## 7. Start the local development server

````bash
php artisan serve
````
Your app should now be accessible at:
👉 http://127.0.0.1:8000

## 🛠️ Auto Setup (Windows)

A `setup.bat` script is included to automate the installation process.
Simply run:

```bash
setup.bat
````
## 📦 Features

- 🥦 Product & Category Management
- 🛒 Shopping Cart (Session & Database)
- 💳 Checkout with PayPal Integration
- ⭐ Product Reviews
- ❤️ Wishlist
- 🤖 AI Chatbot with Gemini API
- 📊 Admin Dashboard

## 🤝 Contribution

1. Fork the repository
2. Create a new branch
   ```bash
   git checkout -b feature-branch
3. Commit your changes
    ```bash
    git commit -m "Add new feature"

4. Push to the branch
    ```bash
    git push origin feature-branch

5. Create a Pull Request

## 📜 License

This project is the property of **Viet Huan**.  
Unauthorized copying, modification, or distribution is not allowed without permission.



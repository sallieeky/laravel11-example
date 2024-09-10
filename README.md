# Installed With Granule Starter Kit

This application is build with [Granule Starter Kit](https://github.com/pupukkaltim/granule-starter-kit), a modern tool for building web applications on PT Pupuk Kalimantan Timur. It provides a complete setup for both client-side and server-side development, ensuring scalability, maintainability, and efficiency right from the get-go, It also implement the standard coding style and best practices for building web applications.

## 📋 Table of Contents
- [Installed With Granule Starter Kit](#installed-with-granule-starter-kit)
  - [📋 Table of Contents](#-table-of-contents)
  - [📝 Description](#-description)
    - [Key Features:](#key-features)
  - [🚀 Installation](#-installation)
    - [Local Environment](#local-environment)

## 📝 Description

This application is a web-based application that provides a platform for users to manage their tasks and projects. It allows users to create, update, and delete tasks, as well as assign tasks to different projects. The application also provides a dashboard view that displays an overview of all tasks and projects, including the number of tasks completed, in progress, and pending.

### Key Features:
- **User Authentication**: Users can sign up and log in to the application using their username and password.
- **User Management**: Users can create, update, delete user, and see user log activities.

## 🚀 Installation

### Local Environment

To get started with the application on your local machine, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/pupukkaltim/<your-repo>.git
   cd <your-repo>
    ```
2. **Install Dependencies:**
    ```bash
    composer install
    npm install
    ```
3. **Create a `.env` File and Set the `.env` File:**
    ```bash
    cp .env.example .env
    ```
4. **Generate an Application Key:**
    ```bash
    php artisan key:generate
    ```
5. **Run Migrations:**
    ```bash
    php artisan migrate --seed
    ```
6. **Run the Application:**
    ```bash
    php artisan serve
    npm run dev
    ```

### Production Environment

To deploy the application to a production environment, follow these steps:

#### Prerequisites

1. **Set Up a Server:**
    - Make sure you have a server with a Linux-based operating system.
    - Make sure your server have an access to your device.
    - Make sure your server installed with docker and docker-compose.

2. **Set Up a Domain Name:**
    - Make sure you have a domain name that points to your server's IP address.
    - Make sure you have an SSL certificate for your domain name.
    - Make sure you have a valid SSL certificate for your domain name.

3. **Set Up a Database:**
    - Make sure your server have a database server installed or have an access to existing database.
    - Make sure you have a database user with the necessary permissions to use the database.

#### Deployment Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/pupukkaltim/<your-repo>.git
   cd <your-repo>
   ```
2. **Create a `.env` File and Set the `.env` File:**
    ```bash
    cp .env.example .env
    ```
3. **Build and Up the Docker:**
    ```bash
    docker-compose up -d --build
    ```
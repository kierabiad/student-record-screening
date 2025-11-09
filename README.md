# Student Record System (Laravel 12 CRUD)

This project is a simple Student Record Management System built with the Laravel 12 framework and MySQL. It implements full CRUD (Create, Read, Update, Delete) functionality for student records, fulfilling the core requirements of the project task.

## 📌 Core Requirements Implemented

* **Technology Stack:** Laravel 12 (PHP) and MySQL.
* **Database Schema:** Students table with unique constraints on `student_id` and `email`.
* **CRUD Operations:** Full functionality for creating, viewing, updating, and deleting records.
* **Validation:** Ensures required fields (`student_id`, `full_name`, `date_of_birth`, `gender`, `email`, `course`, `year_level`) are present and that `student_id` and `email` are unique.

## 🛠️ Setup and Installation

Follow these steps to get the application running on your local development environment.

1.  **Prerequisites**
    Before starting, ensure you have the following installed:
    * PHP (v8.2 or higher)
    * Composer (PHP dependency manager)
    * MySQL Server or a local development stack like XAMPP/WAMP/Laragon.

2.  **Install Dependencies**
    Install all necessary PHP packages using Composer:
    ```bash
    composer install
    ```

3.  **Environment Configuration**
    Modify your `.env` file and update the Database Settings section to match your local MySQL configuration.
    
    Example settings:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=student_database
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Database Setup**
    A. Create the Database: Before running migrations, you must manually create the database named `student_database` in your MySQL server.
    B. Run Migrations: Execute the database migrations to create the students table:
    ```bash
    php artisan migrate
    ```

5.  **Running the Application**
    Start the local development server:
    ```bash
    php artisan serve
    ```
    The application will be accessible at: https://student-record-screening-production.up.railway.app/students

## 🧪 Testing Instructions

* **View Records:** Navigate to the root URL. The main screen displays the student list (initially empty).
* **Create Records (C):** Click "+ Create New Student" and fill out the required fields.
    * *Test Validation:* Try to submit with duplicate Student ID or Email to verify the uniqueness constraints.
* **View Detail (R):** Click the "View" button next to any student record to see the detailed profile modal.
* **Update Records (U):** Click the "Edit" button next to any student record, modify the data, and submit to verify updates.
* **Delete Records (D):** Click the "Delete" button and confirm the action to permanently remove the record.

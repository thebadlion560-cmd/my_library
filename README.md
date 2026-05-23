# Library Management System

A complete full-stack Library Management System built with PHP, MySQL, HTML, CSS, and Bootstrap 5. This system is admin-focused and designed for librarians to manage books, students, and book circulation.

## Features

- **Authentication**: Secure admin login with session management
- **Dashboard**: Real-time statistics showing total books, students, issued/returned books
- **Book Management**: Add, edit, delete, and search books with quantity tracking
- **Student Management**: Add, delete, and view student records
- **Book Issue System**: Issue books to students with automatic quantity tracking
- **Book Return System**: Mark books as returned with automatic quantity restoration
- **Reports**: View issued books, returned books, and overdue books
- **Responsive Design**: Fully responsive UI using Bootstrap 5
- **Security**: Prepared statements for all database queries to prevent SQL injection

## Technology Stack

- **Backend**: PHP (Core PHP, no frameworks)
- **Database**: MySQL
- **Frontend**: HTML, CSS, Bootstrap 5
- **Icons**: Bootstrap Icons

## Project Structure

```
library/
├── admin/
│   ├── dashboard.php          # Admin dashboard with statistics
│   ├── add_book.php           # Add new book
│   ├── manage_books.php       # View, search, delete books
│   ├── edit_book.php          # Edit book details
│   ├── add_student.php        # Add new student
│   ├── manage_students.php    # View and delete students
│   ├── issue_book.php         # Issue book to student
│   ├── return_book.php        # Return issued book
│   ├── reports.php            # View all reports
│   └── logout.php             # Logout functionality
├── assets/
│   ├── css/                   # Custom CSS files
│   ├── js/                    # Custom JavaScript files
│   └── images/                # Image files
├── includes/
│   ├── db.php                 # Database connection
│   ├── header.php             # Reusable header with navigation
│   └── footer.php             # Reusable footer
├── database/
│   └── library.sql            # Database schema and default data
└── index.php                  # Admin login page
```

## Database Structure

### Tables

1. **admins** - Administrator accounts
   - id, name, email, password, created_at

2. **books** - Book inventory
   - id, title, author, category, isbn, quantity, available_quantity, added_date

3. **students** - Student records
   - id, name, email, phone, created_at

4. **issued_books** - Book circulation records
   - id, book_id, student_id, issue_date, return_date, status

## Installation Instructions (XAMPP)

### Prerequisites
- XAMPP installed on your system
- Basic knowledge of running PHP applications

### Step 1: Setup XAMPP

1. Download and install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Create Database

1. Open your browser and go to: http://localhost/phpmyadmin
2. Click on "New" to create a new database
3. Name the database: `library_management`
4. Click "Create"

### Step 3: Import Database Schema

1. In phpMyAdmin, select the `library_management` database
2. Click on "Import" tab
3. Choose the file: `database/library.sql`
4. Click "Go" to import the tables and default admin account

### Step 4: Deploy the Project

1. Navigate to your XAMPP installation directory (usually `C:\xampp\htdocs`)
2. Copy the entire `library` folder to `htdocs`
3. Your project structure should be: `C:\xampp\htdocs\library\`

### Step 5: Access the Application

1. Open your browser
2. Go to: http://localhost/library
3. You will see the login page

### Default Admin Credentials

- **Email**: admin@library.com
- **Password**: admin123

**Important**: Change the default password after first login for security.

## Usage Guide

### 1. Login
- Use the default credentials to login
- The system will redirect you to the dashboard

### 2. Dashboard
- View real-time statistics
- Quick access to all features
- Navigate using the sidebar menu

### 3. Manage Books
- **Add Book**: Click "Add New Book" to add books to the library
- **Edit Book**: Click the pencil icon to edit book details
- **Delete Book**: Click the trash icon to delete a book (only if not issued)
- **Search**: Use the search bar to find books by title, author, or ISBN

### 4. Manage Students
- **Add Student**: Click "Add New Student" to register students
- **Delete Student**: Click the trash icon to remove a student (only if no books issued)

### 5. Issue Books
- Select a student from the dropdown
- Select an available book from the dropdown
- Set the return date
- Click "Issue Book" - the available quantity will automatically decrease

### 6. Return Books
- View all currently issued books
- Click "Return" button to mark a book as returned
- The available quantity will automatically increase

### 7. Reports
- **Issued Books**: View all currently issued books
- **Returned Books**: View history of returned books
- **Overdue Books**: View books that are past their due date

## Security Features

- **Prepared Statements**: All database queries use prepared statements to prevent SQL injection
- **Session Management**: Secure session-based authentication
- **Password Hashing**: Admin passwords are hashed using PHP's password_hash()
- **Input Validation**: All user inputs are validated and sanitized
- **Access Control**: Protected pages redirect to login if not authenticated

## Customization

### Change Database Credentials

Edit `includes/db.php`:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_management";
```

### Change Default Admin Password

1. Login to phpMyAdmin
2. Go to `library_management` database
3. Open `admins` table
4. Edit the admin record
5. Generate a new password hash using:
   ```php
   echo password_hash('your_new_password', PASSWORD_DEFAULT);
   ```
6. Replace the password hash in the database

## Troubleshooting

### Database Connection Error
- Ensure MySQL service is running in XAMPP
- Check database credentials in `includes/db.php`
- Verify the database name is correct

### Session Not Working
- Ensure PHP sessions are enabled in php.ini
- Check folder permissions for session storage

### Page Not Found (404)
- Verify the project is in the correct `htdocs` folder
- Check the URL: http://localhost/library

### Bootstrap Not Loading
- Ensure you have internet connection (Bootstrap is loaded via CDN)
- Or download Bootstrap locally and update the CDN links

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Edge
- Safari
- Mobile browsers (responsive design)

## Future Enhancements

- Email notifications for overdue books
- Fine calculation for late returns
- Book reservation system
- Student borrowing history
- Export reports to PDF/Excel
- Barcode/QR code scanning
- Multi-language support

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please refer to the code comments or contact the development team.

---

**Developed with PHP, MySQL, and Bootstrap 5**

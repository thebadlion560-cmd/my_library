# 🚀 XAMPP Setup Guide - Library Management System

## कैसे XAMPP पर चलाएं? (How to Run on XAMPP)

यह guide आपको step-by-step बताएगी कि Library Management System को XAMPP पर कैसे setup और run करें।

---

## 📋 Prerequisites (आवश्यक चीजें)

- Windows Computer
- XAMPP Server (latest version)
- Web Browser (Chrome, Firefox, etc.)
- Basic understanding of file management

---

## 📥 Step 1: XAMPP Download और Install करें

### XAMPP Download करें:
1. [XAMPP Official Website](https://www.apachefriends.org/) पर जाएं
2. "Download" button पर क्लिक करें
3. Windows version के लिए XAMPP download करें (approx 160 MB)
4. Download complete होने के बाद, installer run करें

### XAMPP Install करें:
1. XAMPP installer double-click करें
2. "Next" button पर क्लिक करें
3. Components select करें (सभी selected रहने दें)
4. Installation folder select करें (default: C:\xampp)
5. "Next" पर क्लिक करें
6. Installation complete होने तक wait करें
7. "Finish" पर क्लिक करें

---

## 🚀 Step 2: XAMPP Control Panel Start करें

1. XAMPP Control Panel open करें (Desktop shortcut से या Start menu से)
2. **Apache** module के आगे "Start" button पर क्लिक करें
3. **MySQL** module के आगे "Start" button पर क्लिक करें
4. दोनों modules green color में change हो जाएंगे

✅ **Important:** दोनों Apache और MySQL running होने चाहिए

---

## 📁 Step 3: Project Files Copy करें

1. XAMPP installation folder में जाएं (usually: `C:\xampp`)
2. `htdocs` folder open करें
3. अपना project folder यहाँ copy करें:
   - Source: `c:\Users\DELL\OneDrive\Desktop\my_library`
   - Destination: `C:\xampp\htdocs\library`

**Files जो copy करने हैं:**
- `database.sql`
- `db.php`
- `login.php`
- `logout.php`
- `layout.php`
- `dashboard.php`
- `add_book.php`
- `manage_books.php`
- `edit_book.php`
- `add_student.php`
- `manage_students.php`
- `edit_student.php`
- `issue_book.php`
- `return_book.php`
- `reports.php`

---

## 🗄️ Step 4: Database Create करें

### phpMyAdmin Open करें:
1. Web browser में यह URL type करें: `http://localhost/phpmyadmin`
2. या XAMPP Control Panel में "Admin" button पर क्लिक करें (MySQL के आगे)

### Database Import करें:
1. phpMyAdmin में "Databases" tab पर क्लिक करें
2. New database name डालें: `library_management`
3. "Create" button पर क्लिक करें
4. अब "Import" tab पर क्लिक करें
5. "Choose File" button पर क्लिक करें
6. `database.sql` file select करें (जो आपने htdocs में copy की है)
7. "Go" button पर क्लिक करें
8. Database successfully import हो जाएगी

✅ **Success message दिखना चाहिए:**
- "Import has been successfully finished"

---

## 🔧 Step 5: Database Connection Verify करें

### db.php File Check करें:
1. `C:\xampp\htdocs\library\db.php` file open करें
2. Database settings check करें:

```php
$host = 'localhost';        // ✅ Correct
$dbname = 'library_management';  // ✅ Database name
$username = 'root';         // ✅ Default XAMPP username
$password = '';             // ✅ Default XAMPP password (empty)
```

अगर आपने MySQL password set किया है, तो `$password` में वो password डालें।

---

## 🌐 Step 6: Website Access करें

### Browser में URL type करें:
```
http://localhost/library/login.php
```

### Default Admin Login:
- **Email:** `admin@library.com`
- **Password:** `admin123`

---

## ✅ Step 7: Testing करें

### Login Test:
1. Login page open होनी चाहिए
2. Default credentials से login करें
3. Dashboard redirect होना चाहिए

### Dashboard Test:
1. Statistics cards दिखने चाहिए
2. Quick action buttons work करने चाहिए
3. Recent activity table दिखनी चाहिए

### Book Management Test:
1. "Manage Books" पर क्लिक करें
2. Sample books दिखने चाहिए
3. "Add Book" से new book add करें
4. Edit और delete buttons test करें

### Student Management Test:
1. "Manage Students" पर क्लिक करें
2. Sample students दिखने चाहिए
3. "Add Student" से new student add करें

### Issue/Return Test:
1. "Issue Book" पर क्लिक करें
2. Book और student select करें
3. Book issue करें
4. "Return Book" से book return करें

---

## 🛠️ Troubleshooting (समस्या समाधान)

### Problem 1: Apache not starting
**Solution:**
- Skype या other applications close करें (port 80 use करते हैं)
- XAMPP Control Panel में Apache config check करें
- Port change करें (Apache → Config → httpd.conf)

### Problem 2: MySQL not starting
**Solution:**
- MySQL service check करें
- Port 3306 free होना चाहिए
- XAMPP reinstall करें अगर problem continue हो

### Problem 3: Database connection failed
**Solution:**
- MySQL running है या check करें
- Database name correct है या verify करें
- Username और password check करें
- db.php file में settings verify करें

### Problem 4: Page not found (404 error)
**Solution:**
- URL correct है या check करें
- Files htdocs folder में हैं या verify करें
- Folder name correct है या check करें
- Apache running है या confirm करें

### Problem 5: White screen / blank page
**Solution:**
- PHP errors enable करें (db.php में already enabled है)
- Browser console check करें (F12)
- File permissions check करें
- PHP syntax errors check करें

---

## 📝 Project Structure

```
C:\xampp\htdocs\library\
│
├── database.sql          # Database file
├── db.php                # Database connection
├── login.php             # Admin login page
├── logout.php            # Logout page
├── layout.php            # Admin layout (sidebar, navbar)
├── dashboard.php         # Admin dashboard
├── add_book.php          # Add new book
├── manage_books.php      # View, edit, delete books
├── edit_book.php         # Edit book details
├── add_student.php       # Add new student
├── manage_students.php   # View, edit, delete students
├── edit_student.php      # Edit student details
├── issue_book.php        # Issue book to student
├── return_book.php       # Return issued book
└── reports.php           # View reports
```

---

## 🔐 Security Notes

### For Development (Current Setup):
- ✅ Simple MD5 password hashing
- ✅ Basic session authentication
- ✅ Prepared statements for SQL injection prevention

### For Production:
- ⚠️ Use bcrypt or Argon2 for password hashing
- ⚠️ Implement CSRF protection
- ⚠️ Add input validation and sanitization
- ⚠️ Use HTTPS/SSL certificate
- ⚠️ Implement rate limiting
- ⚠️ Add email verification
- ⚠️ Regular security updates

---

## 🎯 Quick Reference

### XAMPP Control Panel:
- **Start Apache:** Web server enable
- **Start MySQL:** Database server enable
- **Admin:** phpMyAdmin open करें

### Important URLs:
- **phpMyAdmin:** `http://localhost/phpmyadmin`
- **Project:** `http://localhost/library/login.php`
- **Localhost:** `http://localhost`

### Default Credentials:
- **MySQL Username:** `root`
- **MySQL Password:** (empty)
- **Admin Email:** `admin@library.com`
- **Admin Password:** `admin123`

---

## 📞 Need Help?

### Common Resources:
- [XAMPP Documentation](https://www.apachefriends.org/docs/)
- [PHP Manual](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)

### Error Messages:
- अगर कोई error आती है, error message copy करें
- Google पर search करें
- Stack Overflow पर question post करें

---

## 🎉 Setup Complete!

अब आपका Library Management System XAMPP पर successfully setup हो गया है!

**Next Steps:**
1. Dashboard explore करें
2. Sample books और students add करें
3. Book issue और return test करें
4. Reports section check करें

---

**Happy Learning! 🚀**

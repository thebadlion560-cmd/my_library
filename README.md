# 📚 Library Management System

A complete Library Management System built with HTML, CSS, and JavaScript. This project is designed as a BCA (Bachelor of Computer Applications) project and is easy to deploy on GitHub Pages.

## ✨ Features

- **Add Books**: Add new books with unique ID, title, author, category, and quantity
- **View Books**: Display all books in a organized table format
- **Search Books**: Search books by title, author, or ID
- **Delete Books**: Remove books from the library
- **Issue Books**: Issue books to students with name and roll number tracking
- **Return Books**: Return issued books and update availability
- **Data Persistence**: All data is stored in localStorage for persistence

## 🚀 How to Use

### Local Usage
1. Download or clone this repository
2. Open `index.html` in any modern web browser
3. Start managing your library!

### Deploy on GitHub Pages

#### Step 1: Create a GitHub Repository
1. Go to [GitHub](https://github.com) and sign in to your account
2. Click the **+** icon in the top-right corner
3. Select **New repository**
4. Enter a repository name (e.g., `library-management-system`)
5. Choose **Public** or **Private** (Public is recommended for GitHub Pages)
6. Click **Create repository**

#### Step 2: Upload Files
1. On your new repository page, click **uploading an existing file**
2. Drag and drop all files from this project:
   - `index.html`
   - `style.css`
   - `script.js`
   - `README.md`
3. Add a commit message (e.g., "Initial commit")
4. Click **Commit changes**

#### Step 3: Enable GitHub Pages
1. Go to your repository settings
2. Click on **Pages** in the left sidebar
3. Under **Source**, select **Deploy from a branch**
4. Choose **main** (or **master**) branch and **/ (root)** folder
5. Click **Save**

#### Step 4: Access Your Website
1. Wait a few minutes for deployment
2. GitHub will provide a URL like: `https://yourusername.github.io/library-management-system/`
3. Open this URL in your browser to access your Library Management System!

## 📁 Project Structure

```
library-management-system/
│
├── index.html          # Main HTML file with all sections
├── style.css           # CSS styling for modern UI
├── script.js           # JavaScript functionality
└── README.md           # Project documentation
```

## 🎨 Technologies Used

- **HTML5**: Structure and layout
- **CSS3**: Styling with gradients, animations, and responsive design
- **JavaScript (ES6)**: Functionality and data management
- **localStorage**: Client-side data persistence

## 🌟 Key Features Explained

### Add Book
- Enter unique Book ID, Title, Author, Category, and Quantity
- Automatic validation for duplicate Book IDs
- Success notification on addition

### View Books
- Displays all books in a table format
- Shows Book ID, Title, Author, Category, Available copies, and Total copies
- Delete button for each book

### Search Book
- Real-time search by Book ID, Title, or Author
- Instant results display
- Case-insensitive search

### Issue/Return Books
- Issue books to students with name and roll number
- Track issue date
- Return books and update availability automatically
- View all currently issued books

## 💾 Data Storage

All data is stored in the browser's localStorage, which means:
- Data persists even after closing the browser
- No database setup required
- Easy to use and deploy
- Note: Data is stored locally on each browser/device

## 🎯 BCA Project Requirements Met

- ✅ Complete frontend implementation
- ✅ User-friendly interface
- ✅ CRUD operations (Create, Read, Update, Delete)
- ✅ Data management and persistence
- ✅ Responsive design
- ✅ Easy deployment on GitHub
- ✅ Well-documented code
- ✅ Professional UI/UX

## 📱 Browser Compatibility

Works on all modern browsers:
- Chrome
- Firefox
- Safari
- Edge
- Opera

## 🔧 Customization

You can easily customize:
- **Colors**: Modify the gradient colors in `style.css`
- **Categories**: Add more categories in `index.html` select options
- **Fields**: Add additional fields in the forms
- **Styling**: Update CSS to match your preferences

## 📝 Future Enhancements

Potential improvements for advanced versions:
- Backend database integration
- User authentication
- Fine calculation for late returns
- Email notifications
- Barcode/QR code scanning
- Advanced reporting and analytics

## 👨‍💻 Author

Created as a BCA project demonstrating web development skills.

## 📄 License

This project is open source and available for educational purposes.

---

**Note**: This project uses localStorage for data storage. For production use with multiple users, consider implementing a backend database.

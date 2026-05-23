// Library Management System - BCA Project
// Data stored in localStorage for persistence

// Initialize data from localStorage or use empty arrays
let books = JSON.parse(localStorage.getItem('books')) || [];
let issuedBooks = JSON.parse(localStorage.getItem('issuedBooks')) || [];
let activityLog = JSON.parse(localStorage.getItem('activityLog')) || [];

// Tab switching functionality
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const tabId = btn.getAttribute('data-tab');
        
        // Remove active class from all tabs and contents
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        btn.classList.add('active');
        document.getElementById(tabId).classList.add('active');
        
        // Refresh tables when switching tabs
        if (tabId === 'view-books') {
            displayBooks();
        } else if (tabId === 'issue-book') {
            displayIssuedBooks();
        } else if (tabId === 'dashboard') {
            updateDashboard();
        }
    });
});

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Save data to localStorage
function saveData() {
    localStorage.setItem('books', JSON.stringify(books));
    localStorage.setItem('issuedBooks', JSON.stringify(issuedBooks));
    localStorage.setItem('activityLog', JSON.stringify(activityLog));
}

// Add activity to log
function addActivity(message) {
    const activity = {
        message: message,
        timestamp: new Date().toLocaleString()
    };
    activityLog.unshift(activity);
    // Keep only last 10 activities
    if (activityLog.length > 10) {
        activityLog = activityLog.slice(0, 10);
    }
    saveData();
}

// Update dashboard statistics
function updateDashboard() {
    const totalBooks = books.length;
    const availableBooks = books.reduce((sum, book) => sum + book.available, 0);
    const issuedBooksCount = issuedBooks.length;
    const categories = [...new Set(books.map(book => book.category))].length;
    
    document.getElementById('totalBooks').textContent = totalBooks;
    document.getElementById('availableBooks').textContent = availableBooks;
    document.getElementById('issuedBooksCount').textContent = issuedBooksCount;
    document.getElementById('totalCategories').textContent = categories;
    
    // Display activity log
    const activityLogContainer = document.getElementById('activityLog');
    if (activityLog.length === 0) {
        activityLogContainer.innerHTML = '<p class="no-data">No recent activity</p>';
    } else {
        activityLogContainer.innerHTML = activityLog.map(activity => `
            <div class="activity-item">
                <p><strong>${activity.message}</strong></p>
                <small>${activity.timestamp}</small>
            </div>
        `).join('');
    }
}

// Add Book Form Handler
document.getElementById('addBookForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    const bookId = document.getElementById('bookId').value.trim();
    const bookTitle = document.getElementById('bookTitle').value.trim();
    const bookAuthor = document.getElementById('bookAuthor').value.trim();
    const bookCategory = document.getElementById('bookCategory').value;
    const bookQuantity = parseInt(document.getElementById('bookQuantity').value);
    
    // Check if book ID already exists
    if (books.some(book => book.id === bookId)) {
        showToast('Book ID already exists!', 'error');
        return;
    }
    
    // Add new book
    const newBook = {
        id: bookId,
        title: bookTitle,
        author: bookAuthor,
        category: bookCategory,
        total: bookQuantity,
        available: bookQuantity
    };
    
    books.push(newBook);
    addActivity(`Added book: ${bookTitle} by ${bookAuthor}`);
    saveData();
    
    // Clear form
    document.getElementById('addBookForm').reset();
    
    showToast('Book added successfully!', 'success');
});

// Display all books in table
function displayBooks() {
    const tableBody = document.getElementById('booksTableBody');
    const noBooksMessage = document.getElementById('noBooksMessage');
    
    tableBody.innerHTML = '';
    
    if (books.length === 0) {
        noBooksMessage.classList.add('show');
        return;
    }
    
    noBooksMessage.classList.remove('show');
    
    books.forEach(book => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${book.id}</td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.category}</td>
            <td>${book.available}</td>
            <td>${book.total}</td>
            <td>
                <button class="btn btn-danger" onclick="deleteBook('${book.id}')">Delete</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Delete book
function deleteBook(bookId) {
    if (confirm('Are you sure you want to delete this book?')) {
        const book = books.find(b => b.id === bookId);
        books = books.filter(book => book.id !== bookId);
        addActivity(`Deleted book: ${book.title}`);
        saveData();
        displayBooks();
        showToast('Book deleted successfully!', 'success');
    }
}

// Search functionality
document.getElementById('searchBtn').addEventListener('click', searchBooks);
document.getElementById('searchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') {
        searchBooks();
    }
});

function searchBooks() {
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    const tableBody = document.getElementById('searchTableBody');
    const noResultsMessage = document.getElementById('noSearchResults');
    
    tableBody.innerHTML = '';
    
    if (!searchTerm) {
        noResultsMessage.classList.add('show');
        return;
    }
    
    const filteredBooks = books.filter(book => 
        book.id.toLowerCase().includes(searchTerm) ||
        book.title.toLowerCase().includes(searchTerm) ||
        book.author.toLowerCase().includes(searchTerm)
    );
    
    if (filteredBooks.length === 0) {
        noResultsMessage.classList.add('show');
        return;
    }
    
    noResultsMessage.classList.remove('show');
    
    filteredBooks.forEach(book => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${book.id}</td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.category}</td>
            <td>${book.available}</td>
            <td>${book.total}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Issue Book Form Handler
document.getElementById('issueBookForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    const bookId = document.getElementById('issueBookId').value.trim();
    const studentName = document.getElementById('studentName').value.trim();
    const studentRoll = document.getElementById('studentRoll').value.trim();
    
    // Check if book exists
    const book = books.find(b => b.id === bookId);
    if (!book) {
        showToast('Book not found!', 'error');
        return;
    }
    
    // Check if book is available
    if (book.available <= 0) {
        showToast('No copies available!', 'error');
        return;
    }
    
    // Check if student already has this book
    if (issuedBooks.some(ib => ib.bookId === bookId && ib.studentRoll === studentRoll)) {
        showToast('Student already has this book!', 'error');
        return;
    }
    
    // Issue the book
    const issueDate = new Date().toLocaleDateString();
    const issuedBook = {
        bookId: bookId,
        bookTitle: book.title,
        studentName: studentName,
        studentRoll: studentRoll,
        issueDate: issueDate
    };
    
    issuedBooks.push(issuedBook);
    book.available--;
    addActivity(`Issued book "${book.title}" to ${studentName} (${studentRoll})`);
    saveData();
    
    // Clear form
    document.getElementById('issueBookForm').reset();
    
    showToast('Book issued successfully!', 'success');
    displayIssuedBooks();
});

// Return Book Form Handler
document.getElementById('returnBookForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    const bookId = document.getElementById('returnBookId').value.trim();
    
    // Check if book is issued
    const issuedIndex = issuedBooks.findIndex(ib => ib.bookId === bookId);
    if (issuedIndex === -1) {
        showToast('Book is not currently issued!', 'error');
        return;
    }
    
    // Find the book and update availability
    const book = books.find(b => b.id === bookId);
    if (book) {
        book.available++;
    }
    
    // Remove from issued books
    const returnedBook = issuedBooks[issuedIndex];
    issuedBooks.splice(issuedIndex, 1);
    addActivity(`Returned book "${returnedBook.bookTitle}"`);
    saveData();
    
    // Clear form
    document.getElementById('returnBookForm').reset();
    
    showToast('Book returned successfully!', 'success');
    displayIssuedBooks();
});

// Display issued books
function displayIssuedBooks() {
    const tableBody = document.getElementById('issuedBooksTableBody');
    const noIssuedBooks = document.getElementById('noIssuedBooks');
    
    tableBody.innerHTML = '';
    
    if (issuedBooks.length === 0) {
        noIssuedBooks.classList.add('show');
        return;
    }
    
    noIssuedBooks.classList.remove('show');
    
    issuedBooks.forEach(issuedBook => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${issuedBook.bookId}</td>
            <td>${issuedBook.bookTitle}</td>
            <td>${issuedBook.studentName}</td>
            <td>${issuedBook.studentRoll}</td>
            <td>${issuedBook.issueDate}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    updateDashboard();
    displayBooks();
    displayIssuedBooks();
});

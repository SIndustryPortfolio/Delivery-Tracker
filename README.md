# Delivery Tracking Platform  
### Raw PHP • QR Tracking • Google Maps

A **raw PHP delivery tracking website** that allows orders to be **registered, tracked, and managed** by different user levels.  
The system is built with **security, scalability, and clarity** in mind, without relying on a full framework.

It supports real-time tracking views, QR-based order access, and role-based management of deliveries.

---

## 🚚 Overview

This platform provides a complete delivery workflow:

- Order registration and management
- Public tracking via QR-generated URLs
- Secure user authentication
- Role-based access control
- Interactive map-based parcel visualisation

Designed to handle **hundreds of active orders** efficiently while remaining lightweight and maintainable.

---

## 🧩 Tech Stack

- **Backend:** Raw PHP (Custom Framework)
- **Database:** MySQL
- **Frontend:** Bootstrap + custom CSS
- **Interactivity:** JQuery + AJAX
- **Maps:** Google Maps API
- **Templating:** Layered `.phtml` structure

---

## 🚀 Core Features

---

### 👤 Users & Roles

The platform supports multiple permission levels:

| Role       | Capabilities |
|-----------|--------------|
| Viewer    | View tracking status via URL or QR |
| Deliverer | Manage assigned deliveries |
| Admin     | Full system access and user management |

All access is enforced server-side.

---

### 📦 Orders & Tracking

- Register new delivery orders
- View and manage order status
- Assign deliveries to deliverers
- Track parcel progress in real time

---

### 🔗 QR Code Tracking

- Each order generates a **unique QR code**
- QR codes resolve to a **tracking URL**
- Public-facing tracking page:
  - No login required
  - Read-only access
  - Status and destination visibility

---

### 🗺️ Maps & Location

- Interactive maps using **Google Maps**
- Custom map markers for parcel destinations
- Browser-based location grabbing (with user consent)
- Visual representation of delivery routes and endpoints

---

### 🔄 Pagination & Large Data Handling

- Ordered and paginated datasets
- Optimised queries for large datasets
- Designed to comfortably handle **100s of active orders**

---

### 🔍 Real-Time Search

- Live data filtering using **Search Filtering** + AJAX
- Search through:
  - Orders
  - Users
  - Tracking IDs
- No full page reloads

---

## 🔐 Security

Security is implemented at every layer.

### Authentication & Sessions

- Custom session tokens generated per client
- Secure, time-based session cookies
- Token validation on every protected request

---

### Request & Input Protection

- Strict request filtering (`GET`, `POST`, etc.)
- Input sanitisation on all user-supplied data
- Non-injectable SQL values
- SQL Injection prevention via prepared statements
- No unauthorised route access

---

### Account Management

- Register users
- Login / Logout
- Edit user profiles
- Delete accounts (permission-based)

---

## 🎨 Frontend Architecture

### Responsive Design

- Built with **Bootstrap**
- Fully responsive layouts
- Mobile-friendly tracking pages

---

### Modular Structure

- Layered `.phtml` templates
- Separated CSS files per module
- Clean separation of:
  - Layouts
  - Views
  - Components

This keeps the codebase readable and scalable despite being framework-free.

---

## 🗄️ Database Design

- **MySQL**
- Normalised schema
- Foreign key relationships between:
  - Users
  - Roles
  - Orders
  - Deliveries
  - Tracking records

Designed to maintain data integrity and performance at scale.

---

## 🛠️ Setup (High-Level)

1. Clone the repository  
2. Configure database credentials  
3. Import the SQL schema  
4. Configure Google Maps API key  
5. Set secure cookie/session settings  
6. Serve via Apache or Nginx  

*(Detailed setup steps can be added as required.)*

---

## 📦 Design Philosophy

- Framework-free, explicit PHP
- Strong separation of concerns
- Security-first architecture
- Predictable, readable code
- Built to scale without complexity

---

## 📜 License

MIT (or replace with your preferred license)

---

## ✨ Notes

This project demonstrates:

- Secure raw PHP architecture
- Role-based access control
- QR-driven public tracking
- Scalable database design
- Real-world delivery management workflows

A solid foundation for **logistics, courier, or internal delivery systems**.

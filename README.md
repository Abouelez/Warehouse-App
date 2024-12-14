# Warehouse-App
## Features

This Warehouse Management System is built entirely from scratch to handle inventory, transactions, user authentication, and more. Below are the key features:

---

### 1. Custom Routing System
- Built-in routing system supporting dynamic parameters, middleware, and HTTP methods (`GET`, `POST`, `PUT`, `DELETE`).
- Easily add new endpoints for scalability.

---

### 2. Secure Authentication
- **JWT (JSON Web Token)**-based authentication for secure user login and route protection.
- Password hashing for enhanced security.
- Role-based access control to protect sensitive operations.

---

### 3. Inventory Management
- Full CRUD operations for managing items in the warehouse.
- Track stock quantities in real-time, including additions, subtractions, and returns.

---

### 4. Stock Transaction Tracking
- Record every stock movement (in, out, or returned) with timestamped transactions.
- Retrieve transaction history for specific items or types.

---

### 5. Unit of Measurement (UOM) Management
- Add, update, delete, and view units of measurement like kilograms, pieces, etc.
- Ensure accurate inventory tracking with proper UOMs.

---

### 6. Validation System
- Robust validation for API inputs, including required fields, unique constraints, and type checking.
- Ensures consistent and clean data storage.

---

### 7. Pagination
- Built-in pagination for large datasets in API responses.
- Includes `current_page`, `prev_page`, and `next_page` links for easy navigation.

---

### 8. Reusable API Response System
- Standardized JSON responses for all endpoints.
- Includes error handling and success messages for consistent API behavior.

---

### 9. User Management
- Manage user profiles with endpoints for updating and retrieving user information.
- Secure logout functionality to invalidate tokens.

---

### 10. Lightweight and Scalable Design
- Built with a modular architecture for scalability.
- Optimized for easy future feature integration.

---

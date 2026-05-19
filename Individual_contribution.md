# Individual Project Documentation  
## Admin Module – Food Redistribution Management System

---

# Project Information

| Field | Details |
|---|---|
| Project Name | Food Redistribution Management System |
| Module Name | Admin Panel Module |
| Developed By | Team Leader |
| Technology Stack | PHP, MySQL, HTML, CSS, Bootstrap |
| Development Environment | XAMPP |
| Database | MySQL |

---

# Individual Contribution Overview

As the **Team Leader** of the project, my primary responsibilities included:

- Developing the complete **Admin Module**
- Creating and managing database tables
- Writing SQL queries and handling database integration
- Merging all team members’ modules into a single project
- Managing the GitHub repository and resolving merge conflicts
- Creating the project README and technical documentation
- Performing system testing and debugging
- Maintaining the overall project architecture and workflow

---

# Admin Module Overview

The Admin Module serves as the central management system of the Food Redistribution Management Platform. It allows administrators to monitor, manage, and control all major functionalities of the system.

The module is responsible for:

- Managing users and donors
- Monitoring food donations
- Handling delivery information
- Managing food requests
- Supervising system activities

---

# Admin Module File Structure

```bash
admin/
│
├── dashboard.php
├── delivery.php
├── food.php
├── request.php
└── users.php
```

---

# 1. dashboard.php

## Purpose

The `dashboard.php` file acts as the main control panel for the administrator. It displays overall system statistics and provides quick access to all administrative functionalities.

---

## Core Functionalities

- Displays total users
- Displays total donors
- Displays total food donations
- Displays total food requests
- Displays delivery statistics
- Provides quick navigation to admin operations

---

## Workflow

1. Admin logs into the system
2. Session authentication is verified
3. Database queries fetch system statistics
4. Results are displayed dynamically on dashboard cards

---

## SQL Queries Used

### Total Users

```sql
SELECT COUNT(*) FROM users;
```

### Total Food Donations

```sql
SELECT COUNT(*) FROM food;
```

### Total Requests

```sql
SELECT COUNT(*) FROM requests;
```

---

# 2. delivery.php

## Purpose

The `delivery.php` file manages all food delivery operations and delivery records.

---

## Core Functionalities

- Displays delivery records
- Tracks delivery status
- Monitors receiver information
- Updates delivery status

---

## Workflow

1. Admin accesses delivery panel
2. Delivery records are fetched from database
3. Admin reviews and updates delivery information
4. Updated information is stored in database

---

## SQL Queries Used

### Retrieve Delivery Data

```sql
SELECT * FROM delivery;
```

### Update Delivery Status

```sql
UPDATE delivery
SET status = 'Delivered'
WHERE id = 1;
```

---

# 3. food.php

## Purpose

The `food.php` file manages all donated food records within the system.

---

## Core Functionalities

- Displays donated food items
- Monitors food quantity and availability
- Removes invalid or expired food entries
- Tracks donor submissions

---

## Workflow

1. Donor submits food donation
2. Data is stored in database
3. Admin reviews donation records
4. Admin manages food availability and visibility

---

## SQL Queries Used

### Retrieve Food Data

```sql
SELECT * FROM food;
```

### Delete Food Record

```sql
DELETE FROM food
WHERE id = 1;
```

---

# 4. request.php

## Purpose

The `request.php` file manages food requests submitted by receivers.

---

## Core Functionalities

- Displays food requests
- Approves or rejects requests
- Tracks request status
- Monitors receiver information

---

## Workflow

1. Receiver submits food request
2. Request information is stored in database
3. Admin reviews request details
4. Request status is updated accordingly

---

## SQL Queries Used

### Retrieve Request Data

```sql
SELECT * FROM requests;
```

### Approve Request

```sql
UPDATE requests
SET status = 'Approved'
WHERE id = 1;
```

---

# 5. users.php

## Purpose

The `users.php` file manages all system users and their activities.

---

## Core Functionalities

- Displays registered users
- Manages donor and receiver accounts
- Removes unauthorized users
- Tracks user activities

---

## Workflow

1. User registration data is stored in database
2. Admin accesses user management panel
3. User records are monitored and controlled
4. Necessary updates or deletions are performed

---

## SQL Queries Used

### Retrieve User Data

```sql
SELECT * FROM users;
```

### Delete User

```sql
DELETE FROM users
WHERE id = 1;
```

---

# Database Contribution

As part of my individual contribution, I created and managed the following database tables and related SQL queries.

---

# Donors Table

## Purpose

The `donors` table stores information related to food donors.

---

## Table Structure

| Field Name | Data Type | Description |
|---|---|---|
| id | INT | Primary Key |
| name | VARCHAR(100) | Donor Name |
| email | VARCHAR(100) | Donor Email |
| phone | VARCHAR(20) | Contact Number |
| address | VARCHAR(255) | Donor Address |

---

## SQL Query

```sql
CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address VARCHAR(255)
);
```

---

# Users Table

## Purpose

The `users` table stores authentication and role-based user information.

---

## Table Structure

| Field Name | Data Type | Description |
|---|---|---|
| id | INT | Primary Key |
| username | VARCHAR(100) | User Name |
| email | VARCHAR(100) | User Email |
| password | VARCHAR(255) | Encrypted Password |
| role | VARCHAR(50) | User Role |

---

## SQL Query

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    role VARCHAR(50)
);
```

---

# Database Relationships

## User Relationship

- One user contains one specific role
- Roles include:
  - Admin
  - Donor
  - Receiver

---

## Donor Relationship

- One donor can donate multiple food items
- Relationship Type:
  - One-to-Many

---

# Security Features Implemented

## Session Management

Used PHP sessions for secure authentication.

```php
session_start();
```

---

## Input Validation

Implemented input sanitization and validation to reduce security vulnerabilities such as:
- SQL Injection
- Cross-Site Scripting (XSS)

---

## Authentication Control

Restricted unauthorized access to admin pages using session verification.

---

# Challenges Faced During Development

During development and integration, several technical challenges were encountered:

- Database connectivity issues
- Role-based authentication handling
- Module integration conflicts
- GitHub merge conflicts
- Maintaining consistent UI design across modules

---

# Solutions Implemented

The following solutions were applied:

- Centralized database configuration
- Structured modular development
- Version control using GitHub
- Reusable PHP components
- Extensive testing and debugging

---

# Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Backend Development |
| MySQL | Database Management |
| HTML | Page Structure |
| CSS | Styling |
| Bootstrap | Responsive Design |
| XAMPP | Local Development Server |
| GitHub | Version Control |

---

# Final Project Outcome

The Admin Module successfully provides:

- Centralized management of the entire platform
- Efficient monitoring of food donations and requests
- User and donor management system
- Delivery tracking system
- Secure administrative control panel

The module plays a critical role in ensuring smooth operation of the Food Redistribution Management System.

---

# Personal Contribution Summary

As the Team Leader, my contributions included:

- Developing the complete Admin Module
- Creating donor and user database tables
- Writing and managing SQL queries
- Merging all team modules into a unified system
- Managing project repository and version control
- Creating project documentation and README
- Performing debugging and final testing
- Coordinating the entire project workflow

---

# Conclusion

The Admin Module was designed to provide a secure, organized, and efficient management system for the Food Redistribution Management Platform. Through proper database management, role-based control, and modular development, the system successfully supports food donation and distribution activities while maintaining administrative efficiency and system reliability.

---

# FoodShare BD

## Professional Team Documentation & Viva Preparation Guide

### Leftover Food Redistribution & Donation Management System

---

# Document Information

| Item                | Details                                |
| ------------------- | -------------------------------------- |
| Project Name        | FoodShare BD                           |
| Project Type        | Web-Based Food Redistribution Platform |
| Academic Purpose    | Database Management System Lab Project |
| Technology Stack    | PHP, MySQL, Bootstrap 5, CSS3          |
| Development Pattern | Modular Role-Based Architecture        |
| Database Engine     | MySQL                                  |
| Team Structure      | Collaborative Multi-Module Development |
| Version             | Final Submission                       |
| Status              | Fully Functional Prototype             |

---

# Executive Summary

FoodShare BD is a smart food redistribution platform designed to minimize food wastage by connecting food donors with receivers through a centralized management system. The application supports multiple user roles including Admin, Donor, Receiver, and Delivery personnel.

The platform allows restaurants, event organizers, and households to donate excess food, while NGOs and needy individuals can request food in real time. The system also includes request approval workflows, delivery tracking, inventory synchronization, quantity management, and status monitoring.

The overall objective of this project is to:

* Reduce food waste
* Support food accessibility
* Improve donation logistics
* Digitize food distribution management
* Create a scalable social impact platform

---

# Table of Contents

1. Introduction
2. Project Objectives
3. System Features
4. Technology Stack
5. Software Architecture
6. Database Design
7. Role-Based Workflow
8. Team Contributions
9. File Structure Explanation
10. Module Breakdown
11. Core Functionalities
12. Security & Validation
13. Database Relationships
14. System Workflow Diagram Explanation
15. Important SQL Operations
16. Viva Preparation Questions & Answers
17. Future Improvements
18. Conclusion

---

# 1. Introduction

Food waste is a major global issue. Many restaurants, hotels, wedding programs, and households dispose of excess food while thousands of people still struggle to get meals.

FoodShare BD was developed to solve this problem through a digital donation and redistribution system.

The system creates a bridge between:

* Food Donors
* Food Receivers
* Delivery Management
* Administrative Monitoring

This project demonstrates:

* Database normalization
* Role-based access control
* CRUD operations
* Session management
* Relational database implementation
* Inventory synchronization
* Workflow automation

---

# 2. Project Objectives

## Primary Objectives

* Reduce food wastage
* Build an organized food donation system
* Ensure fair food distribution
* Track food delivery lifecycle
* Maintain real-time inventory

## Technical Objectives

* Implement secure authentication
* Build a normalized relational database
* Use modular PHP architecture
* Create responsive UI
* Implement quantity tracking system
* Build status-based workflow automation

---

# 3. Core System Features

## Authentication System

* User Registration
* Secure Login
* Session Management
* Role-Based Dashboard Redirect
* Logout System

## Donor Features

* Add Food Donations
* Edit Donation Information
* Track Donation Status
* View Requests on Donated Food
* Manage Food Availability

## Receiver Features

* Browse Available Food
* Search Food by Location
* Send Food Requests
* Track Delivery Progress
* Cancel Pending Requests

## Admin Features

* Manage Users
* Monitor Food Inventory
* Approve or Reject Requests
* Assign Delivery Personnel
* Track Deliveries
* Monitor Platform Analytics

## Delivery Features

* Delivery Assignment
* Status Updates
* Delivery Completion Tracking
* Request Synchronization

---

# 4. Technology Stack

| Layer              | Technology               |
| ------------------ | ------------------------ |
| Frontend           | HTML5, CSS3, Bootstrap 5 |
| Backend            | PHP                      |
| Database           | MySQL                    |
| Styling            | Custom CSS               |
| Icons              | Font Awesome             |
| Server Environment | XAMPP                    |
| Session Handling   | PHP Sessions             |

---

# 5. Software Architecture

## System Architecture Style

The project follows a modular role-based architecture.

Each user role has:

* Separate dashboard
* Separate workflow
* Separate permissions
* Separate functionalities

## Folder Architecture

```text
food_system/
│
├── admin/
├── donor/
├── receiver/
├── delivery/
├── includes/
├── css/
├── login.php
├── register.php
├── logout.php
├── index.php
└── database.sql
```

## Architecture Benefits

* Easy maintenance
* Better scalability
* Clean workflow separation
* Improved security
* Reduced code conflict
* Modular development

---

# 6. Database Design

## Database Name

```sql
food_donation_db
```

## Main Tables

| Table Name | Purpose                          |
| ---------- | -------------------------------- |
| users      | Stores all user accounts         |
| food       | Stores food donation information |
| requests   | Stores receiver requests         |
| delivery   | Tracks delivery operations       |

---

# 6.1 Users Table

Stores all platform users.

| Column     | Description                |
| ---------- | -------------------------- |
| id         | Primary key                |
| name       | User full name             |
| email      | Unique email               |
| password   | Encrypted password         |
| phone      | Contact number             |
| address    | User address               |
| role       | admin / donor / receiver   |
| created_at | Account creation timestamp |

### Relationship

* One donor can add many food items
* One receiver can create many requests

---

# 6.2 Food Table

Stores all food donation information.

| Column      | Description                        |
| ----------- | ---------------------------------- |
| id          | Primary key                        |
| donor_id    | References users table             |
| food_name   | Name of food                       |
| quantity    | Available quantity                 |
| unit        | Measurement unit                   |
| location    | Pickup location                    |
| expiry      | Expiry datetime                    |
| description | Additional notes                   |
| status      | available / reserved / distributed |
| created_at  | Upload timestamp                   |

### Key Concepts

* Inventory management
* Expiry tracking
* Dynamic quantity synchronization

---

# 6.3 Requests Table

Stores all food requests from receivers.

| Column      | Description                               |
| ----------- | ----------------------------------------- |
| id          | Primary key                               |
| food_id     | Requested food                            |
| receiver_id | Receiver user ID                          |
| quantity    | Requested quantity                        |
| message     | Receiver message                          |
| status      | pending / approved / rejected / delivered |
| created_at  | Request timestamp                         |

### Workflow

```text
Pending → Approved → Delivered
Pending → Rejected
```

---

# 6.4 Delivery Table

Tracks delivery operations.

| Column          | Description                        |
| --------------- | ---------------------------------- |
| id              | Primary key                        |
| request_id      | Related request                    |
| delivery_person | Assigned delivery person           |
| contact         | Delivery contact                   |
| delivery_status | assigned / in_progress / completed |
| notes           | Delivery notes                     |
| updated_at      | Last update timestamp              |

---

# 7. Role-Based Workflow

## Admin Workflow

```text
Login
↓
Dashboard
↓
Manage Users
↓
Review Requests
↓
Approve / Reject Requests
↓
Assign Delivery
↓
Track Delivery Progress
```

## Donor Workflow

```text
Login
↓
Add Food Donation
↓
Food Becomes Available
↓
Receiver Sends Request
↓
Admin Reviews Request
↓
Food Reserved / Distributed
```

## Receiver Workflow

```text
Login
↓
Browse Food
↓
Search by Location
↓
Send Request
↓
Wait for Approval
↓
Track Delivery Status
```

## Delivery Workflow

```text
Assigned
↓
Picked Up
↓
In Progress
↓
Completed
```

---

# 8. Team Contributions

# Team Member 1 — Authentication & Core Backend Module

## Responsibilities

* Login system
* Registration system
* Session handling
* Role-based authentication
* Redirect system
* Secure password verification

## Major Files

| File                | Purpose                                |
| ------------------- | -------------------------------------- |
| login.php           | User authentication                    |
| register.php        | New user registration                  |
| logout.php          | Session destruction                    |
| index.php           | Entry redirection                      |
| includes/config.php | Database connection & helper functions |

## Important Functionalities

### Login Validation

* Verifies email and password
* Uses password hashing
* Creates secure session
* Redirects user based on role

### Registration Validation

* Prevents duplicate email
* Validates password length
* Confirms password matching
* Sanitizes user input

---

# Team Member 2 — Donor Module

## Responsibilities

* Food donation system
* Donation management
* Food editing
* Quantity management
* Donor analytics dashboard

## Major Files

| File                | Purpose               |
| ------------------- | --------------------- |
| donor/dashboard.php | Donor analytics       |
| donor/add_food.php  | Add food donation     |
| donor/my_food.php   | View all donated food |
| donor/edit_food.php | Edit food item        |
| donor/requests.php  | View requests on food |

## Key Features

### Add Food Workflow

```text
Donor adds food
↓
Food stored in database
↓
Food status = available
↓
Receivers can browse it
```

### Validation Logic

* Expiry must be future time
* Quantity cannot be below 1
* Required fields validation
* Secure SQL sanitization

---

# Team Member 3 — Receiver Module

## Responsibilities

* Browse available food
* Search & filtering
* Food request system
* Request tracking dashboard
* Delivery progress tracking

## Major Files

| File                        | Purpose           |
| --------------------------- | ----------------- |
| receiver/dashboard.php      | Receiver overview |
| receiver/available_food.php | Browse food       |
| receiver/my_requests.php    | Track requests    |

## Important Features

### Food Search System

Receivers can:

* Search by food name
* Filter by location
* View urgent food
* View expiry countdown

### Request Management

* Prevent duplicate requests
* Track request lifecycle
* Cancel pending requests
* View delivery details

---

# Team Member 4 — Admin Management Module

## Responsibilities

* User management
* Request approval system
* Delivery assignment
* Inventory synchronization
* Platform analytics

## Major Files

| File                | Purpose                 |
| ------------------- | ----------------------- |
| admin/dashboard.php | Admin analytics         |
| admin/users.php     | Manage users            |
| admin/food.php      | Manage food inventory   |
| admin/requests.php  | Approve/reject requests |
| admin/delivery.php  | Delivery management     |

## Key Functionalities

### Request Approval Logic

When admin approves request:

```text
Request approved
↓
Food quantity decreases
↓
Delivery entry created
↓
Food status updated
```

### Quantity Synchronization

The system automatically:

* Reduces food quantity after approval
* Prevents over-requesting
* Rejects requests if quantity insufficient
* Marks food distributed if quantity becomes zero

---

# Team Member 5 — UI/UX & Database Integration Module

## Responsibilities

* CSS styling
* Dashboard design
* Responsive layout
* Sidebar navigation
* Database schema
* ER design support

## Major Files

| File                | Purpose                     |
| ------------------- | --------------------------- |
| css/style.css       | Main application styling    |
| database.sql        | Complete database schema    |
| includes/navbar.php | Shared navigation component |

## Design Features

* Responsive layout
* Modern card design
* Status badges
* Gradient dashboards
* Mobile-friendly UI
* Consistent color palette

---

# 9. File Structure Explanation

## Admin Folder

Handles:

* Platform control
* User monitoring
* Request approval
* Delivery assignment
* Analytics

## Donor Folder

Handles:

* Food upload
* Food editing
* Donation tracking

## Receiver Folder

Handles:

* Food browsing
* Request management
* Delivery tracking

## Includes Folder

Contains shared components:

* Database connection
* Helper functions
* Navbar
* Session management

---

# 10. Core Functionalities

# 10.1 Authentication System

The authentication system uses:

* PHP sessions
* Password hashing
* Role validation
* Secure redirects

### Security Measures

* Password hashing using `password_hash()`
* Password verification using `password_verify()`
* Session-based access control
* Input sanitization

---

# 10.2 Food Request Workflow

```text
Receiver requests food
↓
Request status = pending
↓
Admin reviews request
↓
If quantity sufficient:
    Approved
Else:
    Rejected
↓
Delivery assigned
↓
Delivery completed
↓
Request status = delivered
```

---

# 10.3 Delivery Synchronization

When delivery becomes completed:

* Request status updates to delivered
* Food quantity checked
* Food status synchronized
* Inventory updated automatically

This ensures:

* Real-time inventory tracking
* Accurate food availability
* Workflow consistency

---

# 11. Database Relationships

## Relationships Overview

```text
users
│
├── donor → food
│                 │
│                 └── requests
│                             │
receiver ─────────────────────┘
│
└── delivery
```

## Relationship Types

| Relationship       | Type        |
| ------------------ | ----------- |
| User → Food        | One-to-Many |
| Food → Requests    | One-to-Many |
| User → Requests    | One-to-Many |
| Request → Delivery | One-to-One  |

---

# 12. Important Validation Logic

## Duplicate Request Prevention

A receiver cannot request the same food repeatedly while previous request is active.

## Quantity Validation

The system checks:

```text
Available Quantity >= Requested Quantity
```

If not:

```text
Request automatically rejected
```

## Expiry Validation

Expired food:

* Cannot be requested
* Is highlighted visually
* Removed from available list

---

# 13. User Interface Design

## Design Philosophy

The UI focuses on:

* Simplicity
* Accessibility
* Clean dashboards
* Easy navigation
* Mobile responsiveness

## Visual Features

* Gradient statistic cards
* Responsive sidebar
* Interactive badges
* Bootstrap-based layout
* Modern typography
* Status color coding

---

# 14. Important SQL Concepts Used

## CRUD Operations

* INSERT
* SELECT
* UPDATE
* DELETE

## SQL Features Used

* JOIN operations
* Foreign keys
* Constraints
* Aggregate functions
* Subqueries
* ENUM status management

## Constraint Examples

```sql
FOREIGN KEY (donor_id) REFERENCES users(id)
```

```sql
CHECK (quantity >= 0)
```

---

# 15. Security Implementation

## Implemented Security Features

| Feature             | Purpose                     |
| ------------------- | --------------------------- |
| Password Hashing    | Protect passwords           |
| Session Validation  | Prevent unauthorized access |
| Role Checking       | Restrict dashboard access   |
| Input Sanitization  | Prevent malicious input     |
| Required Validation | Avoid invalid submissions   |

---

# 16. Viva Preparation Questions & Answers

## Q1: Why did you choose this project?

### Answer:

We selected this project because food wastage is a major real-world issue. Our system creates a practical digital solution that connects donors with receivers while ensuring proper inventory and delivery management.

---

## Q2: What architecture does your project follow?

### Answer:

Our project follows a modular role-based architecture where each role has separate workflows, dashboards, and permissions.

---

## Q3: Why did you use MySQL?

### Answer:

MySQL is lightweight, relational, easy to integrate with PHP, supports foreign keys, and is suitable for CRUD-based management systems.

---

## Q4: Explain the relationship between food and requests.

### Answer:

One food item can have multiple requests. Therefore, the relationship between food and requests is One-to-Many.

---

## Q5: How does quantity synchronization work?

### Answer:

When admin approves a request, the requested quantity is automatically deducted from the food quantity. If quantity becomes zero, the food status changes to reserved or distributed.

---

## Q6: Why did you use sessions?

### Answer:

Sessions are used to maintain login state and ensure secure role-based access throughout the application.

---

## Q7: What happens when delivery is completed?

### Answer:

The delivery status becomes completed, request status changes to delivered, and food inventory updates automatically.

---

## Q8: How did you ensure database normalization?

### Answer:

We separated entities into individual tables and used foreign keys to eliminate redundancy and maintain relational integrity.

---

## Q9: What are the advantages of role-based access?

### Answer:

Role-based access improves security, separates responsibilities, and prevents unauthorized operations.

---

## Q10: What future improvements can be added?

### Answer:

Future upgrades may include:

* Real-time notifications
* Google Maps integration
* Live delivery tracking
* Mobile application
* AI-based food recommendation
* Expiry prediction system

---

# 17. Future Improvements

## Planned Features

* Email notifications
* SMS alerts
* QR-based delivery verification
* Real-time GPS delivery tracking
* Admin analytics charts
* AI demand prediction
* Multi-language support
* Mobile application integration

---

# 18. Project Strengths

## Technical Strengths

* Clean modular architecture
* Real-time quantity synchronization
* Role-based security
* Responsive modern interface
* Structured database relationships
* Automated delivery workflow

## Social Impact Strengths

* Reduces food wastage
* Supports needy communities
* Encourages food donation culture
* Improves resource utilization

---

# 19. Conclusion

FoodShare BD successfully demonstrates how technology can be used to solve social and logistical problems through database-driven web applications.

The system combines:

* Real-world problem solving
* Database management concepts
* Backend development
* Role-based architecture
* Workflow automation
* Responsive user experience

The project also showcases collaborative software development where different modules are integrated into a complete functional system.

---

# Appendix A — Important File References

| File                        | Function                    |
| --------------------------- | --------------------------- |
| login.php                   | User login                  |
| register.php                | User registration           |
| includes/config.php         | Database & helper functions |
| admin/requests.php          | Request approval logic      |
| admin/delivery.php          | Delivery synchronization    |
| donor/add_food.php          | Add donation                |
| receiver/available_food.php | Food browsing               |
| database.sql                | Complete database schema    |
| css/style.css               | Application styling         |

---

# Appendix B — Important Project Concepts

## Concepts Used

* CRUD Operations
* Foreign Keys
* Database Relationships
* Session Handling
* Role-Based Access Control
* Responsive Design
* Status Synchronization
* Inventory Management
* Request Workflow Automation

---

# Appendix C — Demo Credentials

| Role     | Email                                       | Password |
| -------- | ------------------------------------------- | -------- |
| Admin    | [admin@food.com](mailto:admin@food.com)     | password |
| Donor    | [rahim@donor.com](mailto:rahim@donor.com)   | password |
| Receiver | [ngo@receiver.com](mailto:ngo@receiver.com) | password |

---

# Final Statement

This project demonstrates practical implementation of database systems, backend development, workflow automation, and user-centered application design in a real-world food redistribution environment.

---

Prepared By:
FoodShare BD Development Team

Academic Project Documentation

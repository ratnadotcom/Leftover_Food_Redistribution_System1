# 🍽️ Leftover Food Redistribution System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Status](https://img.shields.io/badge/Status-In%20Progress-orange?style=for-the-badge)

**A web-based system to reduce food waste by connecting food donors with people in need.**

*University Database Lab Project | Group 5 | Department of CSE*

</div>

---

## 📌 Project Overview

The **Leftover Food Redistribution System** is a database-driven web application that helps reduce food waste by enabling restaurants, individuals, and event organizers to donate excess food to NGOs and individuals in need. The system manages the entire process — from food listing and requesting, to delivery tracking and admin oversight.

---

## 👥 Group Members & Responsibilities

| # | Name | GitHub | Role | Responsibility |
|---|------|--------|------|----------------|
| 1 | Mst. Ratna | [@ratnadotcom](https://github.com/ratnadotcom) | Database Lead | Database design, SQL schema, all queries |
| 2 | Rifat |  | Backend Dev | Authentication — Login, Register, Session |
| 3 | Shimul | [@shimul190](https://github.com/shimul190) | Backend Dev | Food module — Add, Edit, Delete food items |
| 4 | Nihad | — | Backend Dev | Request module — Request food, approve/reject |
| 5 | Mahmod | — | Frontend Lead | UI design, Admin dashboard, Integration |

---

## 🎯 Features

### 🔐 Authentication
- User registration as Donor or Receiver
- Secure login with session management
- Role-based redirection (Admin / Donor / Receiver)
- Logout system

### 🧑‍🍳 Donor Features
- Add food donations (name, quantity, location, expiry time)
- Edit and delete food posts
- View all posted donations with status
- Approve or reject receiver requests

### 🤲 Receiver Features
- Browse all available food donations
- Search and filter food by location
- Request food with quantity and message
- Track request and delivery status in real time

### 🛠️ Admin Features
- View and manage all users (donors & receivers)
- Manage all food donations across the platform
- Approve or reject food requests
- Assign and track delivery status

---

## 🗄️ Database Design

### Entity Relationship Summary

<img width="724" height="462" alt="image" src="https://github.com/user-attachments/assets/4c16dcbb-78a1-472d-8d2c-2e5f38bd529c" />


### Tables

| Table | Description | Key Fields |
|-------|-------------|------------|
| `Users` | Login accounts for all roles | user_id, email, password, role |
| `Donors` | Donor profile details | donor_id, user_id, donor_type |
| `Receivers` | Receiver profile details | receiver_id, user_id, receiver_type |
| `Food_Items` | Food donation listings | food_id, donor_id, expiry_time, status |
| `Requests` | Food requests by receivers | request_id, food_id, receiver_id, status |
| `Deliveries` | Delivery tracking | delivery_id, request_id, delivery_person, status |

---

## 🗂️ Folder Structure

```
Leftover_Food_Redistribution_System/
│
├── 📁 database/
│   └── leftover_food_complete.sql    ← Full schema + all queries + sample data
│
├── 📁 includes/
│   ├── config.php                    ← DB connection
│   ├── session_check.php             ← Session guard
│   └── navbar.php                    ← Shared navigation
│
├── 📁 admin/
│   ├── dashboard.php                 ← Admin overview & stats
│   ├── users.php                     ← Manage all users
│   ├── food.php                      ← Manage all food items
│   ├── requests.php                  ← Approve / Reject requests
│   └── delivery.php                  ← Delivery management
│
├── 📁 donor/
│   ├── dashboard.php                 ← Donor home
│   ├── add_food.php                  ← Post new donation
│   ├── edit_food.php                 ← Edit food item
│   ├── my_food.php                   ← View own donations
│   └── requests.php                  ← Requests on my food
│
├── 📁 receiver/
│   ├── dashboard.php                 ← Receiver home
│   ├── available_food.php            ← Browse & request food
│   └── my_requests.php               ← Track request status
│
├── 📁 css/
│   └── style.css                     ← Main stylesheet
│
├── index.php                         ← Entry point (auto-redirect)
├── login.php                         ← Login page
├── register.php                      ← Registration page
├── logout.php                        ← Logout & session destroy
└── README.md                         ← This file
```

---

## ⚙️ How to Run Locally

### Requirements
- XAMPP or WAMP (PHP 7.4+, MySQL 5.7+)
- Any modern browser (Chrome, Firefox, Edge)

### Step 1 — Start XAMPP
Open **XAMPP Control Panel** → Start **Apache** and **MySQL**

### Step 2 — Copy project files
Extract the project and copy the folder into:
```
C:\xampp\htdocs\Leftover_Food_Redistribution_System\
```

### Step 3 — Import the database
1. Open browser → go to `http://localhost/phpmyadmin`
2. Click **Import** tab
3. Choose `database/leftover_food_complete.sql`
4. Click **Go**

### Step 4 — Configure DB connection
Open `includes/config.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // add your password if set
define('DB_NAME', 'leftover_food_db');
```

### Step 5 — Open in browser
```
http://localhost/Leftover_Food_Redistribution_System/
```

---

## 🔑 Demo Login Accounts

| Role | Email | Password |
|------|-------|----------|
| 👑 Admin | admin@food.com | admin123 |
| 🍱 Donor | rahim@gmail.com | rahim123 |
| 🤲 Receiver | ngo@bd.org | ngo123 |

---

## 🛠️ Technology Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5.3 |
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ |
| Server | Apache (via XAMPP) |
| Version Control | Git & GitHub |

---

## 📊 Database Query Summary

The `database/leftover_food_complete.sql` file contains:

- ✅ **6 CREATE TABLE** statements with constraints & foreign keys
- ✅ **INSERT** queries — sample data for all tables
- ✅ **SELECT** queries — simple, filtered, JOIN, GROUP BY, LIKE search
- ✅ **UPDATE** queries — status changes, profile updates
- ✅ **DELETE** queries — remove records safely
- ✅ **8 Advanced Report Queries** — admin dashboard, food waste stats, delivery tracking, daily logs

---

## 📅 Development Timeline

| Day | Task | Member |
|-----|------|--------|
| Day 1 | Database setup, folder structure, base template | Ratna + All |
| Day 2 | Login, Register, Session system | Rifat |
| Day 3 | Food add, edit, delete module | Shimul |
| Day 4 | Request & matching system | Nihad |
| Day 5 | Admin dashboard + Delivery tracking | Mahmod + Ratna |
| Day 6 | Integration, testing, bug fixes | All |
| Day 7 | Final report, ER diagram, submission | All |

---

## 📷 Screenshots

> *(Add screenshots here after completing the UI)*
> 
> Example:
> - Login Page
<img width="289" height="337" alt="image" src="https://github.com/user-attachments/assets/252376da-dce6-4085-8992-cd4dad843a52" />
<img width="290" height="306" alt="image" src="https://github.com/user-attachments/assets/f102d0a0-eaf7-4279-b2dc-79dc9203aad2" />


> - Admin Dashboard
<img width="686" height="196" alt="image" src="https://github.com/user-attachments/assets/1c884684-fdbd-4a6e-999a-984ad0e4a25e" />

> - Donor — Add Food
<img width="385" height="273" alt="image" src="https://github.com/user-attachments/assets/da3572c9-4c29-4680-b7de-fce044215d62" />

> - Receiver — Browse Food
<img width="446" height="177" alt="image" src="https://github.com/user-attachments/assets/b3ca1a97-db9d-441b-b503-3a8375481018" />

> - Delivery Tracking
<img width="510" height="153" alt="image" src="https://github.com/user-attachments/assets/2d41187f-95c3-4ee4-b1d3-e909ecff6734" />


---

## 🔮 Future Improvements

- Email/SMS notifications when request is approved
- Google Maps integration for pickup locations
- Mobile app version
- Real-time chat between donor and receiver
- Food rating and feedback system

---

## 📄 License

This project is developed for **academic purposes only** as part of a university database lab course.

---

<div align="center">
Made with ❤️ by Group 5 | CSE Department
</div>

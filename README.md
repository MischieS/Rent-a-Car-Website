# DREAMS RENT - Car Rental Web Application

DREAMS RENT is a full-featured car rental web application built with PHP, MySQL, and Bootstrap 5. It supports user accounts, role-based access, car listings, and a complete booking workflow. Admins can manage users, vehicles, and reservations via a secure dashboard.

## Features

### User

- Secure registration and login (hashed passwords)
- View personal dashboard with active bookings and total spent
- Browse cars and place reservations
- Filter reservations by status: active, ended, or cancelled
- View reservation details

### Admin

- Dashboard with stats and quick access to management tools
- Manage all users and roles
- Add, edit, or remove vehicles
- View and manage all reservations

### Booking Workflow

1. Browse available vehicles
2. Select rental dates and locations
3. Confirm booking (user info auto-filled)
4. View confirmation and rental history

## Technologies Used

- PHP 8+
- MySQL (via XAMPP)
- Bootstrap 5
- JavaScript / jQuery
- FontAwesome & Bootstrap Icons
- Flatpickr (for date selection)
- SweetAlert2 (for modal alerts)
- Prepared SQL statements (for security)

## Folder Structure

```

/cardealer/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│       ├── cars/
│       └── profiles/
│   └── includes/
│       ├── header.php
│       ├── footer.php
│       ├── header\_link.php
│       └── footer\_link.php
├── backend/
│   ├── db\_connect.php
│   ├── update\_user\_profile.php
│   ├── update\_password.php
├── user\_dashboard.php
├── user\_bookings.php
├── user\_settings.php
├── admin\_dashboard.php
├── admin\_users.php
├── admin\_bookings.php
├── admin\_cars.php
├── booking\_checkout.php
├── booking\_detail.php
├── booking\_success.php
├── booking\_list.php
├── login.php
├── register.php

```

## Database Overview

- **users**: Stores user credentials, roles, profile data
- **cars**: Vehicle inventory with details and pricing
- **reservations**: Booking records with user, car, date range, and status
- **locations**: Pickup/return location names

## How to Run Locally

1. Clone the repository:

```

git clone [https://github.com/yourusername/dreams-rent.git](https://github.com/yourusername/dreams-rent.git)

````

2. Import the SQL database into phpMyAdmin or another MySQL client.

3. Update database credentials in `backend/db_connect.php`:

```php
$conn = new mysqli("localhost", "root", "", "cardealer");
````

4. Start Apache and MySQL using XAMPP.

5. Visit the project in your browser:

   ```
   http://localhost/cardealer/login.php
   ```

## Project Status

The application is functional and actively developed. User and admin workflows are implemented. Styling is fully responsive with Bootstrap 5. Future updates will enhance mobile UX and reporting.

## License

This project is open-source and available under the MIT License.

```

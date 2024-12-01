DROP DATABASE IF EXISTS gym_portal;

CREATE DATABASE gym_portal;

USE gym_portal;

CREATE TABLE admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    roll_number INT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipment (
    equipment_id INT PRIMARY KEY,
    equipment_name VARCHAR(100) NOT NULL,
    equipment_type VARCHAR(50)
);

CREATE TABLE slots (
    slot_id INT AUTO_INCREMENT PRIMARY KEY,
    slot_time TIME NOT NULL,
    booking_date DATE NOT NULL,
    is_booked BOOLEAN DEFAULT FALSE, 
    roll_number INT,
    equipment_id INT,
    
    CONSTRAINT unique_booking UNIQUE (equipment_id, booking_date, slot_time),
    CONSTRAINT unique_student UNIQUE (roll_number, booking_date, slot_time),
    
    FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE,
    FOREIGN KEY (roll_number) REFERENCES students(roll_number) ON DELETE CASCADE 
);

CREATE TABLE feedback (
    roll_number INT,
    feedback_id INT ,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (roll_number, feedback_id),
    FOREIGN KEY (roll_number) REFERENCES students(roll_number) ON DELETE CASCADE
);


CREATE TABLE fitness_groups (
    group_id INT PRIMARY KEY,
    group_name VARCHAR(100) unique NOT NULL,
    group_description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE members (
    group_id INT,
    roll_number INT,
    PRIMARY KEY (group_id, roll_number),
    
    FOREIGN KEY (group_id) REFERENCES fitness_groups(group_id) ON DELETE CASCADE, 
    FOREIGN KEY (roll_number) REFERENCES students(roll_number) ON DELETE CASCADE
);






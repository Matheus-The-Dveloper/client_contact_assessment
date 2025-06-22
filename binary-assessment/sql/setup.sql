-- Create the database
CREATE DATABASE IF NOT EXISTS binarycity;
USE binarycity;

-- Drop tables if they exist (for testing purposes)
DROP TABLE IF EXISTS client_contact;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS contacts;

-- Create clients table
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(6) NOT NULL UNIQUE
);

-- Create contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

-- Create client_contact junction table
CREATE TABLE client_contact (
    client_id INT NOT NULL,
    contact_id INT NOT NULL,
    PRIMARY KEY (client_id, contact_id),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
);

CREATE DATABASE IF NOT EXISTS `contacts-db`;
USE `contacts-db`;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL
);

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type BOOLEAN NOT NULL, -- 0 = Telefone, 1 = Email
    description VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_user_contact FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
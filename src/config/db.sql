show DATABASES;
drop database kari;
create database kari;

use kari;
SHOW TABLEs;




drop table logement;
drop table Review;
drop table reservation;
drop table Favoris;
drop table reclamations;
drop table images;
drop table users;







CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,    
    role ENUM('admin', 'traveller', 'host') DEFAULT 'traveller',
    phone VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE logement (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_owner INT NOT NULL,
    price DOUBLE NOT NULL,
    address VARCHAR(255),
    isReserved BOOLEAN,
    reserved_from DATETIME,
    resrved_to DATETIME,
    FOREIGN KEY (id_owner) REFERENCES users(id) ON DELETE CASCADE
);


create table Review(
    id int PRIMARY key AUTO_INCREMENT,
    contenu VARCHAR(50) not NULL,
    id_writer int not NULL,
    id_log int not NULL,
    data_publication DATETIME not NULL,
    Foreign Key (id_log) REFERENCES logement(id) ON DELETE CASCADE
);



CREATE TABLE reservation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL, 
    id_log INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_log) REFERENCES logement(id) ON DELETE CASCADE
);

CREATE TABLE Favoris(
    id int PRIMARY key AUTO_INCREMENT,
    id_log int not NULL,
    id_voy int not null,
    date_fav DATE DEFAULT (CURRENT_DATE()),
    Foreign Key (id_log) REFERENCES logement(id) ON DELETE CASCADE,
    Foreign Key (id_voy) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_logement INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_logement) REFERENCES logement(id) ON DELETE CASCADE,
    INDEX idx_logement (id_logement),
    INDEX idx_primary (is_primary)
);

CREATE TABLE reclamations(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_log INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_log) REFERENCES logement(id) ON DELETE CASCADE
);

CREATE TABLE avis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_reservation INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_reservation) REFERENCES reservation(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review_per_reservation (id_reservation)
);

CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    type VARCHAR(50) NOT NULL, 
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

SELECT * from logement;
SELECT * from Review;
SELECT * from reservation;
SELECT * from users;
SELECT * FROM images;
SELECT * FROM reclamations;


-- alter table users add COLUMN phone varchar(8);

drop database kari;
create database kari;

use kari;

show DATABASES;
SHOW TABLEs;


drop table users;
drop table logement;
drop table Review;
drop table reservation;
drop table Favoris;

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
    FOREIGN KEY (id_owner) REFERENCES users(id) ON DELETE CASCADE
);


create table Review(
    id int PRIMARY key AUTO_INCREMENT,
    contenu VARCHAR(50) not NULL,
    id_writer int not NULL,
    id_log int not NULL,
    data_publication DATETIME not NULL,
    Foreign Key (id_log) REFERENCES Logement(id)
);



CREATE TABLE reservation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL, 
    id_log INT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_log) REFERENCES logement(id)
);

CREATE TABLE Favoris(
    id int PRIMARY key AUTO_INCREMENT,
    id_log int not NULL,
    id_voy int not null,
    date_fav DATE DEFAULT (CURRENT_DATE()),
    Foreign Key (id_log) REFERENCES logement(id),
    Foreign Key (id_voy) REFERENCES users(id)
);


SELECT * from logement;
SELECT * from review;
SELECT * from reservation;
SELECT * from users;

-- alter table users add COLUMN phone varchar(8);

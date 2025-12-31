create database kari;

use kari;

show DATABASES;
SHOW TABLEs;

DROP table utilisateur;
drop table Voyageur;
drop table Hote;
drop table logement;
drop table Review;
drop table Reservation;
create table Admin(
    id int PRIMARY key AUTO_INCREMENT,
    firstname VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    email VARCHAR(20) NOT NULL,
    password VARCHAR(50) NOT NULL,
    role VARCHAR(20) DEFAULT 'Admin'
);

create table Voyageur(
    id int PRIMARY key AUTO_INCREMENT,
    firstname VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    email VARCHAR(20) NOT NULL,
    password VARCHAR(50) NOT NULL,
    role VARCHAR(20) DEFAULT 'Voyageur'
);

create table Hote(
    id int PRIMARY key AUTO_INCREMENT,
    firstname VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    email VARCHAR(20) NOT NULL,
    password VARCHAR(50) NOT NULL,
    role VARCHAR(20) DEFAULT 'Hote'
);


CREATE TABLE logement(
    id int PRIMARY KEY AUTO_INCREMENT,
    id_owner int not null,
    id_buyer int not NULL,
    price DOUBLE not NULL,
    adress VARCHAR(50),
    Foreign Key (id_owner) REFERENCES Hote(id),
    Foreign Key (id_buyer) REFERENCES Voyageur(id)
);


create table Review(
    id int PRIMARY key AUTO_INCREMENT,
    contenu VARCHAR(50) not NULL,
    id_writer int not NULL,
    id_log int not NULL,
    data_publication DATETIME not NULL,
    Foreign Key (id_writer) REFERENCES Voyageur(id),
    Foreign Key (id_log) REFERENCES Logement(id)
);



create table Reservation(
    id int PRIMARY key AUTO_INCREMENT,
    id_buyer int not NULL,
    id_log int not NULL,
    start_date DATETIME not NULL,
    end_date DATETIME,
    Foreign Key (id_buyer) REFERENCES Voyageur(id),
    Foreign Key (id_log) REFERENCES Logement(id)
);

CREATE TABLE Favoris(
    id int PRIMARY key AUTO_INCREMENT,
    id_log int not NULL,
    id_voy int not null,
    date_fav DATE DEFAULT (CURRENT_DATE()),
    Foreign Key (id_log) REFERENCES logement(id),
    Foreign Key (id_voy) REFERENCES Voyageur(id)
);


SELECT * from admin;
SELECT * from voyageur;
SELECT * from hote;
SELECT * from logement;
SELECT * from review;
SELECT * from reservation;
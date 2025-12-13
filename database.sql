-- Tạo database
CREATE DATABASE IF NOT EXISTS shop

-- Thêm trường User(Admin)
create table Users
(
id int PRIMARY KEY AUTO_INCREMENT ,
username varchar(50) UNIQUE NOT NULL ,
password varchar(50) NOT NULL ,
fullname varchar(50) ,
address varchar(50) DEFAULT NULL,
role ENUM('admin','user') not null,
)
-- Thêm data vào user
INSERT INTO `Users`(`username`, `password`, `fullname`, `address`, `role`) VALUES ('admin123','tnanh1407','','','admin')
INSERT INTO `Users`(`username`, `password`, `fullname`, `address`, `role`) VALUES ('user123','tnanh1407','','','user')
-- Thêm trường sản phẩm
create table if not EXISTS Products
(
    id int PRIMARY KEY AUTO_INCREMENT ,
    name varchar(50) UNIQUE NOT NULL ,
    type ENUM('rau_cu','rau_qua','trai_cay'),
    status ENUM('con_hang','het_hang') NOT NULL,
    price double not null,
    image varchar(255) DEFAULT null,
    productDesc varchar(255), 
    nation varchar(255)
);

-- Thêm trường giỏ hàng
CREATE TABLE IF NOT EXISTS Cart
(
    id  INT PRIMARY KEY AUTO_INCREMENT,
    idUser INT,
    idProduct INT,
    quantity INT NOT NULL,
    price DOUBLE NOT NULL,
    FOREIGN KEY (idUser) REFERENCES Users(id),
    FOREIGN KEY (idProduct) REFERENCES Products(id)
);


-- Thêm trường yêu thích
CREATE TABLE IF NOT EXISTS Favourite
(
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    productId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES Users(id),
    FOREIGN KEY (productId) REFERENCES Products(id)
);




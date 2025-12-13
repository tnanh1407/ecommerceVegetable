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

--- Các Sản Phẩm Loại RAU CỦ (rau_cu)
INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Cà rốt Đà Lạt', 'rau_cu', 'con_hang', 18500.00, 'carrot_dalat.jpg', 'Cà rốt tươi, củ to, vị ngọt tự nhiên, thích hợp làm nước ép.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Khoai tây giống Hà Lan', 'rau_cu', 'con_hang', 22000.00, 'khoaitay_halan.jpg', 'Khoai tây củ đều, vỏ mỏng, thích hợp chiên hoặc hầm.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Hành tây tím', 'rau_cu', 'con_hang', 28000.00, 'onion_purple.png', 'Hành tây màu tím, vị cay nhẹ, thường dùng làm salad.', 'Trung Quốc');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Nấm kim châm Hàn Quốc', 'rau_cu', 'het_hang', 15000.00, 'enoki_mushroom.jpg', 'Nấm kim châm đóng gói, hàng nhập khẩu.', 'Hàn Quốc');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Bắp cải trắng', 'rau_cu', 'con_hang', 12500.00, 'bapcai_trang.jpg', 'Bắp cải cuộn chặt, tươi giòn, dùng để luộc hoặc xào.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Củ dền', 'rau_cu', 'con_hang', 31000.00, 'beetroot.jpg', 'Củ dền đỏ, giàu chất sắt, thích hợp làm nước ép.', 'Việt Nam');

--- Các Sản Phẩm Loại RAU QUẢ (rau_qua)
INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Dưa chuột baby', 'rau_qua', 'con_hang', 27500.00, 'cucumber_baby.jpg', 'Dưa chuột loại nhỏ, ăn giòn ngọt, không cần gọt vỏ.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Ớt chuông xanh', 'rau_qua', 'con_hang', 42000.00, 'bellpepper_green.jpg', 'Ớt chuông màu xanh tươi, dùng xào hoặc nướng.', 'Mỹ');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Bí đỏ hồ lô', 'rau_qua', 'con_hang', 19000.50, 'bido_holo.jpg', 'Bí đỏ loại hồ lô, vị béo, thường dùng nấu chè hoặc súp.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Cà tím Nhật Bản', 'rau_qua', 'het_hang', 35000.00, 'eggplant_japanese.png', 'Cà tím dài, vỏ mỏng, hàng nhập khẩu.', 'Nhật Bản');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Đậu cô ve', 'rau_qua', 'con_hang', 24000.00, 'green_beans.jpg', 'Đậu cô ve tươi non, thu hoạch trong ngày.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Mướp hương', 'rau_qua', 'con_hang', 16000.00, 'muophuong.jpg', 'Mướp hương có mùi thơm đặc trưng, dùng nấu canh.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Khổ qua rừng', 'rau_qua', 'het_hang', 30000.00, 'bitter_melon.jpg', 'Khổ qua (Mướp đắng) rừng, vị đắng thanh.', 'Việt Nam');

--- Các Sản Phẩm Loại TRÁI CÂY (trai_cay)
INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Cam sành', 'trai_cay', 'con_hang', 55000.00, 'orange_sanh.jpg', 'Cam sành Bến Tre, nhiều nước, vị ngọt.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Xoài cát Hòa Lộc', 'trai_cay', 'con_hang', 85000.00, 'mango_hoaloc.jpg', 'Xoài cát Hòa Lộc loại 1, chín tự nhiên.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Kiwi vàng New Zealand', 'trai_cay', 'con_hang', 165000.00, 'kiwi_gold.jpg', 'Kiwi vàng nhập khẩu, vị ngọt dịu, giàu vitamin C.', 'New Zealand');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Dâu tây Mộc Châu', 'trai_cay', 'het_hang', 95000.00, NULL, 'Dâu tây tươi Mộc Châu, được hái tận vườn.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Lê Hàn Quốc', 'trai_cay', 'con_hang', 130000.00, 'pear_korean.jpg', 'Lê Hàn Quốc to, giòn, ngọt, mọng nước.', 'Hàn Quốc');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Bưởi da xanh', 'trai_cay', 'con_hang', 70000.00, 'pomelo_green.jpg', 'Bưởi da xanh ruột hồng, múi to, vị ngọt thanh.', 'Việt Nam');

INSERT INTO Products (name, type, status, price, image, productDesc, nation) 
VALUES 
('Chuối tiêu hồng', 'trai_cay', 'con_hang', 30000.00, 'chuoihong_tieu.jpg', 'Chuối tiêu hồng chín tự nhiên, thơm ngon.', 'Việt Nam');

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




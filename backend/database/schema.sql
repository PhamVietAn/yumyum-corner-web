CREATE DATABASE IF NOT EXISTS yumyum_corner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE yumyum_corner;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL,
  original_price INT NULL,
  image_url VARCHAR(500) NOT NULL,
  description TEXT NOT NULL,
  rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
  review_count INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 100,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  address_detail VARCHAR(255) NULL,
  address_district VARCHAR(150) NULL,
  address_city VARCHAR(150) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(30) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS address_detail VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS address_district VARCHAR(150) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS address_city VARCHAR(150) NULL;

CREATE TABLE IF NOT EXISTS carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_carts_user (user_id),
  CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cart_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cart_items_cart_product (cart_id, product_id),
  CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(40) NOT NULL UNIQUE,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  payment_method VARCHAR(40) NOT NULL DEFAULT 'COD',
  shipping_address VARCHAR(500) NOT NULL,
  status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  shipping_fee INT NOT NULL DEFAULT 0,
  subtotal INT NOT NULL DEFAULT 0,
  total_amount INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_orders_customer_email (customer_email)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  quantity INT NOT NULL,
  unit_price INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT INTO categories (id, name, image_url) VALUES
  (1, 'Snack', '/src/assets/home/snacks_4689118.png'),
  (2, 'Đồ uống', '/src/assets/home/soda_1967382.png'),
  (3, 'Trái cây sấy', '/src/assets/home/fruits_11827865.png'),
  (4, 'Kẹo & Bánh', '/src/assets/home/sweet-stuff_16467373.png'),
  (5, 'Hạt dinh dưỡng', '/src/assets/home/mortar_18385012.png'),
  (6, 'Kem & Đông lạnh', '/src/assets/home/ice-cream_11747642.png')
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  image_url = VALUES(image_url);

INSERT INTO products (id, category_id, name, price, original_price, image_url, description, rating, review_count, stock) VALUES
  (1, 1, 'Khoai tây chiên vị muối biển', 25000, 30000, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349849/mww9rxdn5fsbysnzrb8d.jpg', 'Khoai tây chiên giòn rụm với vị muối biển tự nhiên.', 4.8, 156, 100),
  (2, 4, 'Bánh quy chocolate chip', 35000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349848/wghnyqnqekpjzryccyqf.jpg', 'Bánh quy chocolate mềm mại với những mảnh socola thơm ngon.', 4.9, 234, 100),
  (3, 4, 'Kẹo dẻo trái cây hỗn hợp', 18000, 22000, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349848/smlr14pnwdba03ncdnhu.jpg', 'Kẹo dẻo nhiều màu sắc với hương vị trái cây tự nhiên.', 4.7, 189, 100),
  (4, 3, 'Trái cây sấy hỗn hợp', 45000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776351534/ytfimo6oizjlnqjgmx5l.jpg', 'Trái cây sấy giữ nguyên hương vị tự nhiên.', 5.0, 98, 100),
  (5, 2, 'Nước tăng lực Redbull', 20000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349848/rucekzhzxfqxesokvbgt.jpg', 'Nước tăng lực giúp tỉnh táo và nạp năng lượng nhanh chóng.', 4.6, 267, 100),
  (6, 1, 'Bỏng ngô bơ', 15000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349848/ebdfdkszrqjsmfivgfdi.jpg', 'Bỏng ngô bơ thơm ngon, giòn tan.', 4.5, 145, 100),
  (7, 5, 'Hạt hỗn hợp cao cấp', 55000, 65000, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349848/bovx1cqri3cwkucppfld.jpg', 'Hạt dinh dưỡng cao cấp bao gồm hạnh nhân, điều, macca.', 4.9, 178, 100),
  (8, 4, 'Gấu dẻo nhiều màu', 22000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349847/dfzkjpltbwa5kwe4xjuc.jpg', 'Kẹo dẻo hình gấu đáng yêu với nhiều màu sắc.', 4.8, 312, 100),
  (9, 6, 'Kem que socola', 12000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349847/hywpwgsknvl9vpf841eq.jpg', 'Kem que socola mát lạnh, ngọt ngào.', 4.7, 289, 100),
  (10, 2, 'Nước ép trái cây tươi', 28000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349846/df8nnf0yvts5tacrytv3.jpg', 'Nước ép trái cây tươi 100% không đường.', 4.9, 203, 100),
  (11, 4, 'Bánh quy giòn mặn', 32000, NULL, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349847/yfrjne5r7hkmxofwwsdl.jpg', 'Bánh quy giòn mặn hảo hạng.', 4.6, 167, 100),
  (12, 1, 'Snack Hàn Quốc Mix', 40000, 48000, 'https://res.cloudinary.com/dipqklgci/image/upload/v1776349848/y4vre34nzxsijwebzgus.jpg', 'Combo snack Hàn Quốc đa dạng với nhiều hương vị độc đáo.', 5.0, 421, 100),
  
ON DUPLICATE KEY UPDATE
  category_id = VALUES(category_id),
  name = VALUES(name),
  price = VALUES(price),
  original_price = VALUES(original_price),
  image_url = VALUES(image_url),
  description = VALUES(description),
  rating = VALUES(rating),
  review_count = VALUES(review_count),
  stock = VALUES(stock);

INSERT INTO users (full_name, email, phone, address_detail, address_district, address_city, password_hash, role, status, created_at, updated_at)
VALUES ('Admin YumYum', 'admin@yumyum.vn', '', '', '', '', 'Admin@123', 'admin', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  password_hash = VALUES(password_hash),
  role = VALUES(role),
  status = VALUES(status),
  updated_at = NOW();



-- Imported legacy mock data from src/data
-- Default password for imported users: 12345678 (plain text)
INSERT INTO users (full_name, email, phone, address_detail, address_district, address_city, password_hash, role, status, created_at, updated_at)
VALUES
  ('Nguyen Thi Lan','lan.nguyen@gmail.com','0901234567','12/8 Duong Le Loi, Phuong Ben Nghe','Quan 1','TP. Ho Chi Minh','12345678','user','active','2025-06-12 00:00:00',NOW()),
  ('Tran Quang Huy','huy.tran@gmail.com','0912345678','45 Duong Nguyen Van Linh, Phuong Vinh Trung','Quan Thanh Khe','Da Nang','12345678','user','active','2025-11-03 00:00:00',NOW()),
  ('Le Minh Khoa','khoa.le@gmail.com','0988123456','89 Khu pho 3, Phuong Linh Trung','TP Thu Duc','TP. Ho Chi Minh','12345678','user','inactive','2026-01-18 00:00:00',NOW()),
  ('Pham Thu Ha','ha.pham@gmail.com','0977001122','22 Ngo 15 Duong Cau Giay, Phuong Dich Vong','Quan Cau Giay','Ha Noi','12345678','user','active','2024-12-25 00:00:00',NOW()),
  ('Vo Tien Dat','dat.vo@gmail.com','0933556677','17 Duong Hung Vuong, Phuong 9','TP Da Lat','Lam Dong','12345678','user','inactive','2026-03-02 00:00:00',NOW()),
  ('Nguyễn Văn A','nguyenvana@email.com','0901234567','123 Đường ABC, Quận 1, TP.HCM','','','12345678','user','active','2025-08-15 00:00:00',NOW()),
  ('Trần Thị B','tranthib@email.com','0912345678','456 Đường XYZ, Quận 2, TP.HCM','','','12345678','user','active','2025-10-20 00:00:00',NOW()),
  ('Lê Văn C','levanc@email.com','0923456789','789 Đường DEF, Quận 3, TP.HCM','','','12345678','user','active','2025-06-10 00:00:00',NOW()),
  ('Phạm Thị D','phamthid@email.com','0934567890','321 Đường GHI, Quận 4, TP.HCM','','','12345678','user','active','2026-01-05 00:00:00',NOW()),
  ('Hoàng Văn E','hoangvane@email.com','0945678901','654 Đường JKL, Quận 5, TP.HCM','','','12345678','user','inactive','2026-03-20 00:00:00',NOW()),
  ('Đỗ Thị F','dothif@email.com','0956789012','987 Đường MNO, Quận 6, TP.HCM','','','12345678','user','active','2025-09-12 00:00:00',NOW())
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  phone = VALUES(phone),
  address_detail = VALUES(address_detail),
  address_district = VALUES(address_district),
  address_city = VALUES(address_city),
  password_hash = VALUES(password_hash),
  role = VALUES(role),
  status = VALUES(status),
  updated_at = NOW();

INSERT INTO orders (id, order_number, customer_name, customer_email, payment_method, shipping_address, status, order_date, shipping_fee, subtotal, total_amount)
VALUES
  (1,'ORD-2026-001','Nguyễn Văn A','nguyenvana@email.com','COD','123 Đường ABC, Quận 1, TP.HCM','processing','2026-04-01 10:30:00',0,85000,85000),
  (2,'ORD-2026-002','Trần Thị B','tranthib@email.com','Thẻ tín dụng','456 Đường XYZ, Quận 2, TP.HCM','shipped','2026-04-02 14:15:00',0,135000,135000),
  (3,'ORD-2026-003','Lê Văn C','levanc@email.com','Chuyển khoản','789 Đường DEF, Quận 3, TP.HCM','delivered','2026-03-30 09:20:00',0,99000,99000),
  (4,'ORD-2026-004','Phạm Thị D','phamthid@email.com','COD','321 Đường GHI, Quận 4, TP.HCM','pending','2026-04-04 16:45:00',0,108000,108000),
  (5,'ORD-2026-005','Hoàng Văn E','hoangvane@email.com','Momo','654 Đường JKL, Quận 5, TP.HCM','cancelled','2026-04-03 11:00:00',0,80000,80000),
  (6,'ORD-2025-006','Đỗ Thị F','dothif@email.com','COD','987 Đường MNO, Quận 6, TP.HCM','delivered','2025-08-12 09:10:00',0,218000,218000),
  (7,'ORD-2025-007','Bùi Minh G','buiminhg@email.com','Chuyển khoản','75 Đường Nguyễn Kiệm, Gò Vấp, TP.HCM','processing','2025-09-05 14:30:00',0,250000,250000),
  (8,'ORD-2025-008','Võ Thu H','vothuh@email.com','VNPAY','11 Đường Trần Phú, Hải Châu, Đà Nẵng','shipped','2025-09-28 18:05:00',0,285000,285000),
  (9,'ORD-2025-009','Phan Gia I','phangiai@email.com','Thẻ tín dụng','102 Đường 3/2, Ninh Kiều, Cần Thơ','delivered','2025-10-14 10:45:00',0,410000,410000),
  (10,'ORD-2025-010','Ngô Hải K','ngohaik@email.com','Momo','15 Đường Điện Biên Phủ, Hải Châu, Đà Nẵng','cancelled','2025-11-03 08:20:00',0,520000,520000),
  (11,'ORD-2025-011','Trịnh Lệ M','trinhlem@email.com','COD','64 Đường Võ Thị Sáu, Biên Hòa, Đồng Nai','delivered','2025-11-25 16:10:00',0,460000,460000),
  (12,'ORD-2025-012','Lưu Bình N','luubinhn@email.com','Chuyển khoản','28 Đường Hoàng Văn Thụ, TP. Vũng Tàu','processing','2025-12-09 13:55:00',0,505000,505000),
  (13,'ORD-2026-013','Đinh Quốc P','dinhquocp@email.com','VNPAY','90 Đường Lý Thường Kiệt, TP. Huế','delivered','2026-01-17 11:35:00',0,690000,690000),
  (14,'ORD-2026-014','Hà Bảo Q','habaoq@email.com','Thẻ tín dụng','19 Đường Hùng Vương, TP. Mỹ Tho, Tiền Giang','shipped','2026-02-06 15:20:00',0,760000,760000),
  (15,'ORD-2026-015','Phùng Tâm R','phungtamr@email.com','COD','47 Đường Nguyễn Trãi, Nha Trang, Khánh Hòa','pending','2026-03-12 09:50:00',0,820000,820000)
ON DUPLICATE KEY UPDATE
  customer_name = VALUES(customer_name),
  customer_email = VALUES(customer_email),
  payment_method = VALUES(payment_method),
  shipping_address = VALUES(shipping_address),
  status = VALUES(status),
  order_date = VALUES(order_date),
  shipping_fee = VALUES(shipping_fee),
  subtotal = VALUES(subtotal),
  total_amount = VALUES(total_amount),
  updated_at = NOW();

DELETE oi FROM order_items oi
INNER JOIN orders o ON o.id = oi.order_id
WHERE o.order_number LIKE 'ORD-%';

INSERT INTO order_items (order_id, product_id, quantity, unit_price)
VALUES
  (1,1,2,25000),
  (1,2,1,35000),
  (2,4,3,45000),
  (3,7,1,55000),
  (3,8,2,22000),
  (4,12,2,40000),
  (4,10,1,28000),
  (5,5,4,20000),
  (6,3,5,18000),
  (6,11,4,32000),
  (7,12,4,40000),
  (7,6,6,15000),
  (8,7,3,55000),
  (8,4,3,45000),
  (8,9,5,12000),
  (9,1,8,25000),
  (9,2,6,35000),
  (10,8,10,22000),
  (10,5,12,20000),
  (11,10,7,28000),
  (11,3,9,18000),
  (11,11,4,32000),
  (12,12,11,40000),
  (12,7,5,55000),
  (13,4,12,45000),
  (13,6,10,15000),
  (13,2,8,35000),
  (14,1,14,25000),
  (14,12,6,40000),
  (14,8,7,22000),
  (15,5,15,20000),
  (15,7,10,55000),
  (15,10,9,28000)
ON DUPLICATE KEY UPDATE
  quantity = VALUES(quantity),
  unit_price = VALUES(unit_price),
  updated_at = NOW();

CREATE TABLE IF NOT EXISTS product_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  reviewer_name VARCHAR(150) NOT NULL,
  rating TINYINT NOT NULL,
  content TEXT NOT NULL,
  review_date DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO product_reviews (product_id, reviewer_name, rating, content, review_date)
VALUES
  (1,'Nguyễn Thị Mai',5,'Sản phẩm rất ngon, đóng gói cẩn thận. Sẽ ủng hộ shop lần sau!','2026-03-15'),
  (1,'Trần Văn Hùng',4,'Chất lượng tốt, giá cả hợp lý. Giao hàng nhanh.','2026-03-10'),
  (1,'Lê Thị Hoa',5,'Món ăn vặt yêu thích của cả nhà. Đã mua nhiều lần rồi!','2026-03-05'),
  (2,'Phạm Minh Tuấn',5,'Bánh rất giòn và thơm, ăn là ghiền!','2026-03-12'),
  (2,'Hoàng Lan',4,'Chocolate khá nhiều, ngon.','2026-03-09'),
  (3,'Ngọc Anh',5,'Kẹo dẻo nhiều vị, trẻ con rất thích.','2026-03-11'),
  (3,'Minh Phương',4,'Mùi trái cây tự nhiên, dai vừa phải.','2026-03-07'),
  (4,'Minh Đức',3,'Ổn nhưng hơi ngọt.','2026-03-08'),
  (4,'Thanh Tâm',5,'Dẻo ngon, ăn vui miệng và không bị khô.','2026-03-04'),
  (5,'Quang Huy',5,'Uống tỉnh táo hẳn luôn 😆','2026-03-14'),
  (5,'Gia Linh',4,'Vị dễ uống, hợp dùng khi làm việc khuya.','2026-03-06'),
  (6,'Tuấn Kiệt',4,'Bỏng ngô thơm bơ, giòn lâu dù mở gói.','2026-03-13'),
  (6,'Mai Anh',5,'Giá mềm mà chất lượng tốt, sẽ mua lại.','2026-03-09'),
  (7,'Khánh Vy',5,'Hạt tươi, ít vụn, ăn rất bùi.','2026-03-12'),
  (7,'Anh Dũng',4,'Combo hạt đa dạng, đóng túi chắc chắn.','2026-03-08'),
  (8,'Bảo Trâm',5,'Kẹo gấu dẻo ngon, vị trái cây rõ ràng.','2026-03-11'),
  (8,'Hải Nam',4,'Màu sắc đẹp, bé nhà mình rất thích.','2026-03-07'),
  (9,'Thuỳ Dương',5,'Kem socola đậm vị, không quá ngọt.','2026-03-10'),
  (9,'Đức Huy',4,'Mát lạnh, tiện ăn nhanh vào buổi chiều.','2026-03-05'),
  (10,'Yến Nhi',5,'Nước ép thơm tự nhiên, không gắt đường.','2026-03-13'),
  (10,'Văn Phúc',4,'Uống mát, vị trái cây khá tươi.','2026-03-06'),
  (11,'Hoài An',4,'Bánh giòn vừa, vị mặn nhẹ dễ ăn.','2026-03-12'),
  (11,'Quốc Bảo',5,'Rất hợp ăn cùng trà, gói đẹp.','2026-03-08'),
  (12,'Kim Ngân',5,'Mix snack đa dạng, vị nào cũng ổn.','2026-03-14'),
  (12,'Trọng Nhân',4,'Phần combo đầy đặn, ăn đỡ ngán.','2026-03-09');


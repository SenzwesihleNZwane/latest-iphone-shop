-- LATEST-IPHONE e-commerce database (MySQL 8 / MariaDB 10.4+)
-- Import this single file in phpMyAdmin (Import tab). It creates the tables AND fills them with your catalogue.
CREATE DATABASE IF NOT EXISTS latest_iphone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE latest_iphone;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS accessories, layby_payments, layby_accounts, transactions, shipping_details, order_items, orders,
                     branch_stock, products, model_colours, product_models, branches, users, store_items;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE branches (
  branch_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_name VARCHAR(60) NOT NULL UNIQUE,
  province VARCHAR(40) NOT NULL,
  latitude DECIMAL(9,6) NOT NULL,
  longitude DECIMAL(9,6) NOT NULL
) ENGINE=InnoDB;

-- One row per iPhone model (e.g. iPhone 15 Pro)
CREATE TABLE product_models (
  model_id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(30) NOT NULL UNIQUE,
  model_name VARCHAR(80) NOT NULL,
  generation VARCHAR(5) NOT NULL,
  display_spec VARCHAR(120) NULL,
  chip VARCHAR(60) NULL,
  camera VARCHAR(120) NULL,
  battery VARCHAR(60) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- Colour options of a model (each has its own swatch and photo)
CREATE TABLE model_colours (
  colour_id INT AUTO_INCREMENT PRIMARY KEY,
  model_id INT NOT NULL,
  colour_name VARCHAR(40) NOT NULL,
  hex_code CHAR(7) NOT NULL,
  image_path VARCHAR(120) NOT NULL,
  UNIQUE KEY uq_model_colour (model_id, colour_name),
  FOREIGN KEY (model_id) REFERENCES product_models(model_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sellable item = model + storage size (carries the price)
CREATE TABLE products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  model_id INT NOT NULL,
  storage_capacity VARCHAR(10) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  original_price DECIMAL(10,2) NULL,
  condition_status ENUM('New','Pre-owned') NOT NULL DEFAULT 'New',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_model_storage (model_id, storage_capacity),
  FOREIGN KEY (model_id) REFERENCES product_models(model_id) ON DELETE CASCADE,
  CHECK (price > 0)
) ENGINE=InnoDB;

CREATE TABLE branch_stock (
  stock_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity_available INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_branch_product (branch_id, product_id),
  FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
  CHECK (quantity_available >= 0)
) ENGINE=InnoDB;

CREATE TABLE accessories (
  accessory_id INT AUTO_INCREMENT PRIMARY KEY,
  accessory_name VARCHAR(80) NOT NULL,
  category VARCHAR(40) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  icon VARCHAR(30) NOT NULL DEFAULT 'package'
) ENGINE=InnoDB;

CREATE TABLE orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  branch_id INT NOT NULL,
  payment_type ENUM('Full','Layby') NOT NULL DEFAULT 'Full',
  order_status ENUM('Pending','Paid','Layby Active','Dispatched','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  total_amount DECIMAL(10,2) NOT NULL,
  shipping_fee DECIMAL(8,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  colour_name VARCHAR(40) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id)
) ENGINE=InnoDB;

CREATE TABLE shipping_details (
  shipping_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL UNIQUE,
  courier ENUM('PEP Paxi','The Courier Guy') NOT NULL,
  paxi_store_code VARCHAR(20) NULL,
  street_address VARCHAR(150) NULL,
  suburb VARCHAR(80) NULL,
  city VARCHAR(80) NULL,
  postal_code VARCHAR(10) NULL,
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transactions (
  transaction_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  gateway ENUM('PayFast','Ozow','Yoco') NOT NULL,
  payment_method ENUM('Card','Instant EFT') NOT NULL,
  amount_paid DECIMAL(10,2) NOT NULL,
  payment_status ENUM('Pending','Complete','Failed') NOT NULL DEFAULT 'Pending',
  gateway_reference VARCHAR(60) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE layby_accounts (
  layby_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL UNIQUE,
  user_id INT NOT NULL,
  months INT NOT NULL,
  deposit_amount DECIMAL(10,2) NOT NULL,
  monthly_instalment DECIMAL(10,2) NOT NULL,
  balance_remaining DECIMAL(10,2) NOT NULL,
  next_due_date DATE NULL,
  status ENUM('Active','Completed','Defaulted') NOT NULL DEFAULT 'Active',
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  CHECK (months BETWEEN 2 AND 6)
) ENGINE=InnoDB;

CREATE TABLE layby_payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  layby_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (layby_id) REFERENCES layby_accounts(layby_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Seed data (generated from the LatestiPhone catalogue) ----------
INSERT INTO branches (branch_name, province, latitude, longitude) VALUES
('Pretoria','Gauteng',-25.7479,28.2293),
('Durban','KwaZulu-Natal',-29.8587,31.0218),
('Pinetown','KwaZulu-Natal',-29.8149,30.8709),
('Richards Bay','KwaZulu-Natal',-28.7807,32.0383),
('Nelspruit','Mpumalanga',-25.4753,30.97),
('Manguzi','KwaZulu-Natal',-26.9761,32.7513);

INSERT INTO product_models (slug, model_name, generation, display_spec, chip, camera, battery, sort_order) VALUES
('17pm','iPhone 17 Pro Max','17','6.9" Super Retina XDR ProMotion','A19 Pro Bionic','48MP Triple Lens + 5x Telephoto','Up to 33 hrs video',1),
('17p','iPhone 17 Pro','17','6.3" Super Retina XDR ProMotion','A19 Pro Bionic','48MP Triple Lens','Up to 29 hrs video',2),
('17plus','iPhone 17 Plus','17','6.7" Super Retina XDR','A19 Bionic','48MP Dual Fusion Camera','Up to 30 hrs video',3),
('17base','iPhone 17','17','6.1" Super Retina XDR','A19 Bionic','48MP Dual Fusion Camera','Up to 26 hrs video',4),
('16pm','iPhone 16 Pro Max','16','6.9" Super Retina XDR 120Hz','A18 Pro Bionic','48MP Fusion + 5x Optical','Up to 33 hrs video',5),
('16p','iPhone 16 Pro','16','6.3" Super Retina XDR 120Hz','A18 Pro Bionic','48MP Fusion + 3x Optical','Up to 27 hrs video',6),
('16plus','iPhone 16 Plus','16','6.7" Super Retina XDR','A18 Bionic','48MP Dual Lens System','Up to 27 hrs video',7),
('16base','iPhone 16','16','6.1" Super Retina XDR','A18 Bionic','48MP Dual Lens System','Up to 22 hrs video',8),
('15pm','iPhone 15 Pro Max','15','6.7" Titanium Super Retina XDR','A17 Pro Bionic','48MP Triple Lens + 5x Optical','Up to 29 hrs video',9),
('15p','iPhone 15 Pro','15','6.1" Titanium Super Retina XDR','A17 Pro Bionic','48MP Triple Lens + 3x Optical','Up to 23 hrs video',10),
('15plus','iPhone 15 Plus','15','6.7" Dynamic Island Display','A16 Bionic','48MP Dual Main Lens','Up to 26 hrs video',11),
('15base','iPhone 15','15','6.1" Dynamic Island Display','A16 Bionic','48MP Dual Main Lens','Up to 20 hrs video',12),
('14pm','iPhone 14 Pro Max','14','6.7" Super Retina XDR','A16 Bionic','48MP Main Camera','Up to 29 hrs video',13),
('14p','iPhone 14 Pro','14','6.1" Super Retina XDR','A16 Bionic','48MP Main Camera','Up to 23 hrs video',14),
('14plus','iPhone 14 Plus','14','6.7" Super Retina XDR','A15 Bionic','12MP Dual Camera','Up to 26 hrs video',15),
('14base','iPhone 14','14','6.1" Super Retina XDR','A15 Bionic','12MP Dual Camera','Up to 20 hrs video',16),
('13pm','iPhone 13 Pro Max','13','6.7" Super Retina XDR ProMotion','A15 Bionic','12MP Triple Lens System','Up to 28 hrs video',17),
('13p','iPhone 13 Pro','13','6.1" Super Retina XDR ProMotion','A15 Bionic','12MP Triple Lens System','Up to 22 hrs video',18),
('13base','iPhone 13','13','6.1" Super Retina XDR','A15 Bionic','12MP Advanced Dual Camera','Up to 19 hrs video',19),
('12pm','iPhone 12 Pro Max','12','6.7" Super Retina XDR','A14 Bionic','12MP Pro Camera System','Up to 20 hrs video',20),
('12p','iPhone 12 Pro','12','6.1" Super Retina XDR','A14 Bionic','12MP Pro Camera System','Up to 17 hrs video',21),
('12base','iPhone 12','12','6.1" Super Retina XDR OLED','A14 Bionic','12MP Dual System','Up to 17 hrs video',22),
('11pm','iPhone 11 Pro Max','11','6.5" Super Retina XDR','A13 Bionic','12MP Triple Camera System','Up to 20 hrs video',23),
('11p','iPhone 11 Pro','11','5.8" Super Retina XDR','A13 Bionic','12MP Triple Camera System','Up to 18 hrs video',24),
('11base','iPhone 11','11','6.1" Liquid Retina HD','A13 Bionic','12MP Ultra Wide Dual Camera','Up to 17 hrs video',25),
('xsmax','iPhone XS Max','x','6.5" Super Retina OLED','A12 Bionic','12MP Dual Camera System','Up to 15 hrs video',26),
('xs','iPhone XS','x','5.8" Super Retina OLED','A12 Bionic','12MP Dual Camera System','Up to 14 hrs video',27),
('xr','iPhone XR','x','6.1" Liquid Retina HD','A12 Bionic','12MP Single Lens Camera','Up to 16 hrs video',28),
('xbase','iPhone X','x','5.8" Super Retina OLED','A11 Bionic','12MP Dual Optical Zoom','Up to 13 hrs video',29);

INSERT INTO model_colours (model_id, colour_name, hex_code, image_path) VALUES
((SELECT model_id FROM product_models WHERE slug='17pm'),'Titanium Gray','#3f4042','images/iphone-17-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17pm'),'Copper Amber','#d06232','images/iphone-17-pro-max-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17pm'),'Silver Titanium','#e2e4e5','images/iphone-17-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17pm'),'Dark Titanium','#2c2d2e','images/iphone-17-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17p'),'Titanium Gray','#3f4042','images/iphone-17-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17p'),'Copper Amber','#d06232','images/iphone-17-pro-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17p'),'Silver Titanium','#e2e4e5','images/iphone-17-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17p'),'Dark Titanium','#2c2d2e','images/iphone-17-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17plus'),'Mist Blue','#c4dbe0','images/iphone-17-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17plus'),'Soft Pink','#e8cfd8','images/iphone-17-plus-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17plus'),'Sage Green','#b8c4b8','images/iphone-17-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17plus'),'Black','#1f2022','images/iphone-17-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17plus'),'White','#f8f6f0','images/iphone-17-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17base'),'Mist Blue','#c4dbe0','images/iphone-17.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17base'),'Soft Pink','#e8cfd8','images/iphone-17-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17base'),'Sage Green','#b8c4b8','images/iphone-17.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17base'),'Black','#1f2022','images/iphone-17.jpeg'),
((SELECT model_id FROM product_models WHERE slug='17base'),'White','#f8f6f0','images/iphone-17.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16pm'),'Desert Titanium','#c2a991','images/iphone-16-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16pm'),'Natural Titanium','#babbb8','images/iphone-16-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16pm'),'White Titanium','#f8f6f0','images/iphone-16-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16pm'),'Black Titanium','#3c3b37','images/iphone-16-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16p'),'Desert Titanium','#c2a991','images/iphone-16-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16p'),'Natural Titanium','#babbb8','images/iphone-16-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16p'),'White Titanium','#f8f6f0','images/iphone-16-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16p'),'Black Titanium','#3c3b37','images/iphone-16-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16plus'),'Ultramarine','#4862a3','images/iphone-16-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16plus'),'Teal','#a4cbbd','images/iphone-16-plus-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16plus'),'Pink','#e8cfd8','images/iphone-16-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16plus'),'White','#f8f6f0','images/iphone-16-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16plus'),'Black','#1f2022','images/iphone-16-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16base'),'Ultramarine','#4862a3','images/iphone-16.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16base'),'Teal','#a4cbbd','images/iphone-16-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16base'),'Pink','#e8cfd8','images/iphone-16.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16base'),'White','#f8f6f0','images/iphone-16.jpeg'),
((SELECT model_id FROM product_models WHERE slug='16base'),'Black','#1f2022','images/iphone-16.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15pm'),'Natural Titanium','#b8b4ab','images/iphone-15-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15pm'),'Blue Titanium','#2f3843','images/iphone-15-pro-max-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15pm'),'White Titanium','#f2f3f5','images/iphone-15-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15pm'),'Black Titanium','#3b3b3c','images/iphone-15-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15p'),'Natural Titanium','#b8b4ab','images/iphone-15-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15p'),'Blue Titanium','#2f3843','images/iphone-15-pro-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15p'),'White Titanium','#f2f3f5','images/iphone-15-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15p'),'Black Titanium','#3b3b3c','images/iphone-15-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15plus'),'Yellow','#e7e0c4','images/iphone-15-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15plus'),'Pink','#e3c8d0','images/iphone-15-plus-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15plus'),'Blue','#c9d8e0','images/iphone-15-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15plus'),'Green','#d3e0d7','images/iphone-15-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15plus'),'Black','#343538','images/iphone-15-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15base'),'Yellow','#e7e0c4','images/iphone-15.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15base'),'Pink','#e3c8d0','images/iphone-15-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15base'),'Blue','#c9d8e0','images/iphone-15.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15base'),'Green','#d3e0d7','images/iphone-15.jpeg'),
((SELECT model_id FROM product_models WHERE slug='15base'),'Black','#343538','images/iphone-15.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14pm'),'Deep Purple','#4b4351','images/iphone-14-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14pm'),'Gold','#f3e0c8','images/iphone-14-pro-max-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14pm'),'Space Black','#302e2f','images/iphone-14-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14pm'),'Silver','#f0f2f2','images/iphone-14-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14p'),'Deep Purple','#4b4351','images/iphone-14-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14p'),'Gold','#f3e0c8','images/iphone-14-pro-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14p'),'Space Black','#302e2f','images/iphone-14-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14p'),'Silver','#f0f2f2','images/iphone-14-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14plus'),'Blue','#a0b4c8','images/iphone-14-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14plus'),'Purple','#e5d7f2','images/iphone-14-plus-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14plus'),'Midnight','#1f252c','images/iphone-14-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14plus'),'Starlight','#faf6f2','images/iphone-14-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14plus'),'(PRODUCT)RED','#e30016','images/iphone-14-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14plus'),'Yellow','#ffe680','images/iphone-14-plus.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14base'),'Blue','#a0b4c8','images/iphone-14.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14base'),'Purple','#e5d7f2','images/iphone-14-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14base'),'Midnight','#1f252c','images/iphone-14.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14base'),'Starlight','#faf6f2','images/iphone-14.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14base'),'(PRODUCT)RED','#e30016','images/iphone-14.jpeg'),
((SELECT model_id FROM product_models WHERE slug='14base'),'Yellow','#ffe680','images/iphone-14.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13pm'),'Sierra Blue','#9bb5ce','images/iphone-13-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13pm'),'Graphite','#545351','images/iphone-13-pro-max-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13pm'),'Gold','#fae7cf','images/iphone-13-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13pm'),'Silver','#f0f2f2','images/iphone-13-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13pm'),'Alpine Green','#576856','images/iphone-13-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13p'),'Sierra Blue','#9bb5ce','images/iphone-13-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13p'),'Graphite','#545351','images/iphone-13-pro-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13p'),'Gold','#fae7cf','images/iphone-13-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13p'),'Silver','#f0f2f2','images/iphone-13-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13p'),'Alpine Green','#576856','images/iphone-13-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13base'),'Pink','#fadcda','images/iphone-13.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13base'),'Blue','#437691','images/iphone-13-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13base'),'Midnight','#1f252c','images/iphone-13.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13base'),'Starlight','#faf6f2','images/iphone-13.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13base'),'Green','#3c4d3b','images/iphone-13.jpeg'),
((SELECT model_id FROM product_models WHERE slug='13base'),'(PRODUCT)RED','#c8102e','images/iphone-13.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12pm'),'Pacific Blue','#27547d','images/iphone-12-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12pm'),'Graphite','#4a494e','images/iphone-12-pro-max-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12pm'),'Gold','#fae7cf','images/iphone-12-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12pm'),'Silver','#f0f2f2','images/iphone-12-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12p'),'Pacific Blue','#27547d','images/iphone-12-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12p'),'Graphite','#4a494e','images/iphone-12-pro-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12p'),'Gold','#fae7cf','images/iphone-12-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12p'),'Silver','#f0f2f2','images/iphone-12-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12base'),'Blue','#27547d','images/iphone-12.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12base'),'Green','#b5e3d8','images/iphone-12-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12base'),'Black','#1f2022','images/iphone-12.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12base'),'White','#f9f6ef','images/iphone-12.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12base'),'Purple','#d1c4e9','images/iphone-12.jpeg'),
((SELECT model_id FROM product_models WHERE slug='12base'),'(PRODUCT)RED','#d02a3a','images/iphone-12.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11pm'),'Midnight Green','#505e4d','images/iphone-11-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11pm'),'Space Gray','#4a494e','images/iphone-11-pro-max-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11pm'),'Gold','#fae7cf','images/iphone-11-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11pm'),'Silver','#f0f2f2','images/iphone-11-pro-max.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11p'),'Midnight Green','#505e4d','images/iphone-11-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11p'),'Space Gray','#4a494e','images/iphone-11-pro-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11p'),'Gold','#fae7cf','images/iphone-11-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11p'),'Silver','#f0f2f2','images/iphone-11-pro.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11base'),'Purple','#d1c4e9','images/iphone-11.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11base'),'Green','#b5e3d8','images/iphone-11-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11base'),'Yellow','#ffe680','images/iphone-11.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11base'),'Black','#1f2022','images/iphone-11.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11base'),'White','#f9f6ef','images/iphone-11.jpeg'),
((SELECT model_id FROM product_models WHERE slug='11base'),'(PRODUCT)RED','#d02a3a','images/iphone-11.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xsmax'),'Gold','#f1e3d3','images/iphone-xs.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xsmax'),'Space Gray','#4a494e','images/iphone-xs-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xsmax'),'Silver','#e2e4e5','images/iphone-xs.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xs'),'Gold','#f1e3d3','images/iphone-xs.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xs'),'Space Gray','#4a494e','images/iphone-xs-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xs'),'Silver','#e2e4e5','images/iphone-xs.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xr'),'Coral','#ff6b52','images/iphone-xr.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xr'),'Black','#1f2022','images/iphone-xr-2.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xr'),'Blue','#437691','images/iphone-xr.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xr'),'Yellow','#ffe680','images/iphone-xr.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xr'),'White','#f9f6ef','images/iphone-xr.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xr'),'(PRODUCT)RED','#d02a3a','images/iphone-xr.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xbase'),'Silver','#e2e4e5','images/iphone-x.jpeg'),
((SELECT model_id FROM product_models WHERE slug='xbase'),'Space Gray','#4a494e','images/iphone-x-2.jpeg');

INSERT INTO products (model_id, storage_capacity, price, original_price) VALUES
((SELECT model_id FROM product_models WHERE slug='17pm'),'256GB',24900,NULL),
((SELECT model_id FROM product_models WHERE slug='17pm'),'512GB',28400,NULL),
((SELECT model_id FROM product_models WHERE slug='17pm'),'1TB',32900,NULL),
((SELECT model_id FROM product_models WHERE slug='17p'),'128GB',21900,NULL),
((SELECT model_id FROM product_models WHERE slug='17p'),'256GB',24500,NULL),
((SELECT model_id FROM product_models WHERE slug='17p'),'512GB',28900,NULL),
((SELECT model_id FROM product_models WHERE slug='17plus'),'128GB',18500,NULL),
((SELECT model_id FROM product_models WHERE slug='17plus'),'256GB',20900,NULL),
((SELECT model_id FROM product_models WHERE slug='17plus'),'512GB',24900,NULL),
((SELECT model_id FROM product_models WHERE slug='17base'),'128GB',15900,NULL),
((SELECT model_id FROM product_models WHERE slug='17base'),'256GB',17900,NULL),
((SELECT model_id FROM product_models WHERE slug='17base'),'512GB',21500,NULL),
((SELECT model_id FROM product_models WHERE slug='16pm'),'256GB',21500,NULL),
((SELECT model_id FROM product_models WHERE slug='16pm'),'512GB',24900,NULL),
((SELECT model_id FROM product_models WHERE slug='16pm'),'1TB',28900,NULL),
((SELECT model_id FROM product_models WHERE slug='16p'),'128GB',18900,NULL),
((SELECT model_id FROM product_models WHERE slug='16p'),'256GB',21200,NULL),
((SELECT model_id FROM product_models WHERE slug='16p'),'512GB',25400,NULL),
((SELECT model_id FROM product_models WHERE slug='16plus'),'128GB',15500,NULL),
((SELECT model_id FROM product_models WHERE slug='16plus'),'256GB',17800,NULL),
((SELECT model_id FROM product_models WHERE slug='16plus'),'512GB',21900,NULL),
((SELECT model_id FROM product_models WHERE slug='16base'),'128GB',13900,NULL),
((SELECT model_id FROM product_models WHERE slug='16base'),'256GB',15900,NULL),
((SELECT model_id FROM product_models WHERE slug='16base'),'512GB',21500,NULL),
((SELECT model_id FROM product_models WHERE slug='15pm'),'256GB',17900,NULL),
((SELECT model_id FROM product_models WHERE slug='15pm'),'512GB',20500,NULL),
((SELECT model_id FROM product_models WHERE slug='15pm'),'1TB',23900,NULL),
((SELECT model_id FROM product_models WHERE slug='15p'),'128GB',14900,NULL),
((SELECT model_id FROM product_models WHERE slug='15p'),'256GB',16900,NULL),
((SELECT model_id FROM product_models WHERE slug='15p'),'512GB',19900,NULL),
((SELECT model_id FROM product_models WHERE slug='15plus'),'128GB',12900,NULL),
((SELECT model_id FROM product_models WHERE slug='15plus'),'256GB',14500,NULL),
((SELECT model_id FROM product_models WHERE slug='15base'),'128GB',11200,NULL),
((SELECT model_id FROM product_models WHERE slug='15base'),'256GB',12900,NULL),
((SELECT model_id FROM product_models WHERE slug='15base'),'512GB',15900,NULL),
((SELECT model_id FROM product_models WHERE slug='14pm'),'128GB',13900,NULL),
((SELECT model_id FROM product_models WHERE slug='14pm'),'256GB',15500,NULL),
((SELECT model_id FROM product_models WHERE slug='14pm'),'512GB',18900,NULL),
((SELECT model_id FROM product_models WHERE slug='14p'),'128GB',12200,NULL),
((SELECT model_id FROM product_models WHERE slug='14p'),'256GB',13800,NULL),
((SELECT model_id FROM product_models WHERE slug='14plus'),'128GB',10800,NULL),
((SELECT model_id FROM product_models WHERE slug='14plus'),'256GB',12200,NULL),
((SELECT model_id FROM product_models WHERE slug='14base'),'128GB',9900,NULL),
((SELECT model_id FROM product_models WHERE slug='14base'),'256GB',11500,NULL),
((SELECT model_id FROM product_models WHERE slug='13pm'),'128GB',10900,12900),
((SELECT model_id FROM product_models WHERE slug='13pm'),'256GB',12200,14500),
((SELECT model_id FROM product_models WHERE slug='13p'),'128GB',9200,10900),
((SELECT model_id FROM product_models WHERE slug='13p'),'256GB',10500,12500),
((SELECT model_id FROM product_models WHERE slug='13base'),'128GB',7900,9500),
((SELECT model_id FROM product_models WHERE slug='13base'),'256GB',8900,10700),
((SELECT model_id FROM product_models WHERE slug='12pm'),'128GB',8200,9900),
((SELECT model_id FROM product_models WHERE slug='12pm'),'256GB',9400,11200),
((SELECT model_id FROM product_models WHERE slug='12p'),'128GB',7100,8500),
((SELECT model_id FROM product_models WHERE slug='12p'),'256GB',8100,9700),
((SELECT model_id FROM product_models WHERE slug='12base'),'64GB',5800,7000),
((SELECT model_id FROM product_models WHERE slug='12base'),'128GB',6500,7800),
((SELECT model_id FROM product_models WHERE slug='11pm'),'64GB',6200,7500),
((SELECT model_id FROM product_models WHERE slug='11pm'),'256GB',7100,8500),
((SELECT model_id FROM product_models WHERE slug='11p'),'64GB',5400,6500),
((SELECT model_id FROM product_models WHERE slug='11p'),'256GB',6200,7500),
((SELECT model_id FROM product_models WHERE slug='11base'),'64GB',4600,5600),
((SELECT model_id FROM product_models WHERE slug='11base'),'128GB',5300,6400),
((SELECT model_id FROM product_models WHERE slug='xsmax'),'64GB',4800,5800),
((SELECT model_id FROM product_models WHERE slug='xsmax'),'256GB',5500,6600),
((SELECT model_id FROM product_models WHERE slug='xs'),'64GB',4200,5100),
((SELECT model_id FROM product_models WHERE slug='xs'),'256GB',4900,5900),
((SELECT model_id FROM product_models WHERE slug='xr'),'64GB',3800,4600),
((SELECT model_id FROM product_models WHERE slug='xr'),'128GB',4400,5300),
((SELECT model_id FROM product_models WHERE slug='xbase'),'64GB',3400,4100),
((SELECT model_id FROM product_models WHERE slug='xbase'),'256GB',3900,4700);

INSERT INTO accessories (accessory_name, category, price, icon) VALUES
('MagSafe Leather Case','Cases & Covers',899,'shield'),
('Tempered Glass Screen Protector','Protection',349,'square'),
('20W USB-C Fast Charger','Power',549,'zap'),
('MagSafe Wireless Charger','Power',899,'battery-charging'),
('AirPods Pro (2nd Gen)','Audio',4499,'headphones'),
('Braided USB-C Cable (2m)','Cables',299,'cable');

-- Branch stock: every storage option at every branch (deterministic spread of 2-12 units, a few sold out)
INSERT INTO branch_stock (branch_id, product_id, quantity_available)
SELECT b.branch_id, p.product_id,
       CASE WHEN (b.branch_id * 7 + p.product_id * 3) % 13 = 0 THEN 0
            ELSE 2 + ((b.branch_id * 5 + p.product_id * 3) % 11) END
FROM branches b CROSS JOIN products p;

-- Admin account: admin@latest-iphone.co.za / Admin@12345  (change it after your first login!)
INSERT INTO users (full_name, email, phone, password_hash, role) VALUES
('Store Admin','admin@latest-iphone.co.za','0120000000','$2y$10$6cucvTr.FGnp63kYzWuc4OysbFUg0R5eDflTyvYj06hcFqeLJQb/6','admin');

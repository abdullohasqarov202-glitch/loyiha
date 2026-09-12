CREATE DATABASE IF NOT EXISTS smashbite
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE smashbite;

CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50) NOT NULL,
    slug        VARCHAR(50) NOT NULL UNIQUE,
    icon        VARCHAR(20) NOT NULL DEFAULT 'burger',
    sort_order  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id   INT UNSIGNED NOT NULL,
    name          VARCHAR(100) NOT NULL,
    description   VARCHAR(255),
    price         DECIMAL(10,0) NOT NULL,
    icon          VARCHAR(20) NOT NULL DEFAULT 'burger',
    is_available  TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name   VARCHAR(100) NOT NULL,
    phone           VARCHAR(20) NOT NULL,
    order_type      ENUM('delivery','pickup') NOT NULL DEFAULT 'delivery',
    address         VARCHAR(255),
    total_amount    DECIMAL(10,0) NOT NULL,
    payment_method  VARCHAR(30) NOT NULL DEFAULT 'cash',
    payment_status  ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
    status          ENUM('new','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'new',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    quantity    INT UNSIGNED NOT NULL,
    price       DECIMAL(10,0) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name   VARCHAR(100) NOT NULL,
    phone           VARCHAR(20) NOT NULL,
    booking_date    DATE NOT NULL,
    booking_time    TIME NOT NULL,
    guests          INT UNSIGNED NOT NULL DEFAULT 2,
    notes           VARCHAR(255),
    status          ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --- Namunaviy kategoriyalar ---
INSERT INTO categories (name, slug, icon, sort_order) VALUES
('Burgerlar', 'burgers', 'burger', 1),
('Pitsalar', 'pizza', 'pizza', 2),
('Fri va garnirlar', 'sides', 'fries', 3),
('Ichimliklar', 'drinks', 'drink', 4);

-- --- Namunaviy menyu ---
INSERT INTO products (category_id, name, description, price, icon) VALUES
(1, 'Smash Klassik', 'Mol go\'shti, cheddar, achchiq sous, yangi sabzavotlar', 38000, 'burger'),
(1, 'Double Smash', 'Ikki qat mol go\'shti, ikki qat pishloq', 52000, 'burger'),
(1, 'Spicy Chicken', 'Panirovkali tovuq, achchiq mayonez, karam salat', 34000, 'burger'),
(2, 'Pepperoni', 'Pepperoni, mozarella, pomidor sousi', 65000, 'pizza'),
(2, 'Margarita', 'Mozarella, bazilik, pomidor sousi', 55000, 'pizza'),
(3, 'Fri kartoshka', 'Xrustall, tuzlangan', 15000, 'fries'),
(3, 'Mozarella cheese sticks', '6 dona, sous bilan', 22000, 'fries'),
(4, 'Cola 0.5L', 'Muzli, sovuq', 10000, 'drink'),
(4, 'Limonad 0.5L', 'Uy sharoitida tayyorlangan', 12000, 'drink');

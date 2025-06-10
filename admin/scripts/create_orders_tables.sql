-- Create orders table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    `shipping_address` TEXT,
    `payment_method` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create order_items table
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better performance
CREATE INDEX `idx_orders_user` ON `orders`(`user_id`);
CREATE INDEX `idx_orders_status` ON `orders`(`status`);
CREATE INDEX `idx_order_items_order` ON `order_items`(`order_id`);
CREATE INDEX `idx_order_items_product` ON `order_items`(`product_id`);

-- Create view for order summaries
CREATE OR REPLACE VIEW `order_summaries` AS
SELECT 
    o.id,
    o.user_id,
    u.email as user_email,
    o.total_price,
    o.status,
    o.shipping_address,
    o.payment_method,
    o.created_at,
    COUNT(oi.id) as total_items,
    SUM(oi.quantity) as total_quantity
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- Create view for order details
CREATE OR REPLACE VIEW `order_details` AS
SELECT 
    oi.id as item_id,
    oi.order_id,
    oi.product_id,
    p.name as product_name,
    oi.quantity,
    oi.price,
    (oi.quantity * oi.price) as subtotal
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.id;

-- Create trigger to update total price after inserting order items
DELIMITER //
CREATE TRIGGER `after_order_item_insert` 
AFTER INSERT ON `order_items`
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_price = (
        SELECT SUM(quantity * price) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END//

-- Create trigger to update total price after updating order items
CREATE TRIGGER `after_order_item_update` 
AFTER UPDATE ON `order_items`
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_price = (
        SELECT SUM(quantity * price) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END//

-- Create trigger to update total price after deleting order items
CREATE TRIGGER `after_order_item_delete` 
AFTER DELETE ON `order_items`
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_price = (
        SELECT COALESCE(SUM(quantity * price), 0)
        FROM order_items 
        WHERE order_id = OLD.order_id
    )
    WHERE id = OLD.order_id;
END//

-- Create trigger to update product stock after inserting order items
CREATE TRIGGER `after_order_item_insert_stock` 
AFTER INSERT ON `order_items`
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock - NEW.quantity
    WHERE id = NEW.product_id;
END//

-- Create trigger to update product stock after updating order items
CREATE TRIGGER `after_order_item_update_stock` 
AFTER UPDATE ON `order_items`
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock + OLD.quantity - NEW.quantity
    WHERE id = NEW.product_id;
END//

-- Create trigger to update product stock after deleting order items
CREATE TRIGGER `after_order_item_delete_stock` 
AFTER DELETE ON `order_items`
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock + OLD.quantity
    WHERE id = OLD.product_id;
END//
DELIMITER ; 
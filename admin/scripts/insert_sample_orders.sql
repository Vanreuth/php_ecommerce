-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Insert sample users if not exists
INSERT IGNORE INTO `users` (`id`, `email`, `name`, `password`, `role`) VALUES
(1, 'john@example.com', 'John Doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
(2, 'jane@example.com', 'Jane Smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
(3, 'mike@example.com', 'Mike Johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert sample products if not exists
INSERT IGNORE INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category_id`, `brand_id`) VALUES
(1, 'Gaming Laptop', 'High-performance gaming laptop', 1299.99, 10, 1, 1),
(2, 'Wireless Mouse', 'Ergonomic wireless mouse', 49.99, 50, 1, 2),
(3, 'Mechanical Keyboard', 'RGB mechanical keyboard', 129.99, 30, 1, 2),
(4, 'Gaming Monitor', '27-inch 144Hz monitor', 299.99, 15, 1, 3),
(5, 'Gaming Headset', '7.1 surround sound headset', 89.99, 25, 1, 2);

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample orders
INSERT INTO `orders` (`user_id`, `status`, `shipping_address`, `payment_method`, `created_at`) VALUES
(1, 'delivered', '123 Main St, City, Country', 'credit_card', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(2, 'processing', '456 Oak Ave, Town, Country', 'paypal', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(3, 'pending', '789 Pine Rd, Village, Country', 'credit_card', NOW()),
(1, 'shipped', '123 Main St, City, Country', 'credit_card', DATE_SUB(NOW(), INTERVAL 14 DAY)),
(2, 'cancelled', '456 Oak Ave, Town, Country', 'bank_transfer', DATE_SUB(NOW(), INTERVAL 21 DAY));

-- Insert sample order items
-- Order 1 (Delivered) - Gaming setup
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1299.99),  -- Gaming Laptop
(1, 2, 1, 49.99),    -- Wireless Mouse
(1, 3, 1, 129.99);   -- Mechanical Keyboard

-- Order 2 (Processing) - Peripherals
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(2, 2, 2, 49.99),    -- 2x Wireless Mouse
(2, 5, 1, 89.99);    -- Gaming Headset

-- Order 3 (Pending) - Single item
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(3, 4, 1, 299.99);   -- Gaming Monitor

-- Order 4 (Shipped) - Multiple quantities
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(4, 3, 2, 129.99),   -- 2x Mechanical Keyboard
(4, 5, 2, 89.99);    -- 2x Gaming Headset

-- Order 5 (Cancelled) - Various items
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(5, 2, 1, 49.99),    -- Wireless Mouse
(5, 4, 1, 299.99),   -- Gaming Monitor
(5, 5, 1, 89.99);    -- Gaming Headset

-- Verify data insertion
SELECT 'Orders count:', COUNT(*) FROM orders;
SELECT 'Order items count:', COUNT(*) FROM order_items;
SELECT 'Orders by status:' as '', status, COUNT(*) as count 
FROM orders 
GROUP BY status 
ORDER BY count DESC;

-- Sample queries to verify triggers
SELECT o.id, o.status, o.total_price,
       COUNT(oi.id) as items_count,
       SUM(oi.quantity) as total_quantity
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id
ORDER BY o.created_at DESC;

-- Check product stock levels
SELECT p.id, p.name, p.stock,
       SUM(CASE WHEN o.status != 'cancelled' THEN oi.quantity ELSE 0 END) as ordered_quantity
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id
GROUP BY p.id
ORDER BY p.id; 
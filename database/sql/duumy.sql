

-- Insert roles into the roles table
INSERT INTO roles (name, created_at, updated_at) VALUES
('Admin', NOW(), NOW()),
('User', NOW(), NOW());

INSERT INTO users (name, email, password, role_id, created_at, updated_at) VALUES 
('Admin', 'Admin@gmail.com', '$2y$10$Dfj8fKUGdKNCCEsAIHrrJelvEB0IVhPvM3Q4AW26GZnEkfwnJAt7K', 1, NOW(), NOW()), -- Admin
('User', 'user1@example.com', '$2y$10$Dfj8fKUGdKNCCEsAIHrrJelvEB0IVhPvM3Q4AW26GZnEkfwnJAt7K', 2, NOW(), NOW()) -- User
;

INSERT INTO categories (name, description, created_at, updated_at) VALUES
('Electronics', 'Devices and gadgets', NOW(), NOW()),
('Mobile Phones', 'Smartphones and accessories', NOW(), NOW()),
('Clothing', 'Fashion and apparel', NOW(), NOW()),
('Furniture', 'Home and office furniture', NOW(), NOW()),
('Kitchen', 'Kitchen appliances and utensils', NOW(), NOW()),
('Books', 'Various genres of books', NOW(), NOW()),
('Toys', 'Toys for kids and collectors', NOW(), NOW()),
('Games', 'Board and puzzle games', NOW(), NOW());

INSERT INTO products (name, description, price, stock_quantity, category_id, created_at, updated_at) VALUES
('Laptop', 'High performance laptop for work and gaming.', 999.99, 100, 1, NOW(), NOW()),
('Smartphone', 'Latest smartphone with all the modern features.', 799.99, 200, 2, NOW(), NOW()),
('T-Shirt', 'Comfortable cotton t-shirt in various sizes.', 19.99, 500, 3, NOW(), NOW()),
('Sofa', 'Comfortable leather sofa for your living room.', 399.99, 50, 4, NOW(), NOW()),
('Cookware Set', 'High quality cookware set for your kitchen.', 129.99, 30, 5, NOW(), NOW()),
('Novel', 'Fictional story about a thrilling adventure.', 14.99, 150, 6, NOW(), NOW()),
('Action Figure', 'Collectible action figure for fans.', 24.99, 250, 7, NOW(), NOW()),
('Puzzle', 'Jigsaw puzzle for all ages.', 9.99, 300, 8, NOW(), NOW()),
('Headphones', 'Noise-canceling wireless headphones.', 149.99, 80, 2, NOW(), NOW()),
('Smartwatch', 'Smartwatch with health tracking features.', 199.99, 120, 2, NOW(), NOW()),
('Coffee Maker', 'Automatic coffee maker with multiple settings.', 89.99, 70, 5, NOW(), NOW()),
('Blender', 'High-speed blender for smoothies and shakes.', 69.99, 90, 5, NOW(), NOW()),
('Camera', 'Digital camera with high resolution and features.', 499.99, 60, 2, NOW(), NOW()),
('Smart TV', '4K smart TV with streaming capabilities.', 799.99, 40, 4, NOW(), NOW()),
('Laptop Bag', 'Stylish and durable laptop bag for professionals.', 39.99, 150, 1, NOW(), NOW()),
('E-Reader', 'Portable e-reader with long battery life.', 129.99, 200, 6, NOW(), NOW()),
('Tablet', 'Touchscreen tablet with a large display.', 299.99, 100, 2, NOW(), NOW()),
('Wireless Mouse', 'Ergonomic wireless mouse for comfort.', 19.99, 500, 1, NOW(), NOW()),
('Keyboard', 'Mechanical keyboard with customizable keys.', 79.99, 200, 1, NOW(), NOW()),
('Speakers', 'Bluetooth speakers with deep bass and clear sound.', 99.99, 150, 7, NOW(), NOW()),
('Gaming Chair', 'Ergonomic chair designed for gamers.', 249.99, 80, 4, NOW(), NOW()),
('Smart Bulb', 'Smart LED bulbs that you can control with your phone.', 14.99, 300, 8, NOW(), NOW()),
('Projector', 'Portable mini projector for movies and presentations.', 199.99, 40, 4, NOW(), NOW()),
('Electric Kettle', 'Fast boiling electric kettle for your kitchen.', 29.99, 120, 5, NOW(), NOW()),
('Toaster', 'Compact toaster with multiple settings.', 39.99, 200, 5, NOW(), NOW()),
('Microwave', 'Compact microwave oven for easy cooking.', 99.99, 150, 5, NOW(), NOW()),
('Air Purifier', 'Air purifier with HEPA filter for cleaner air.', 149.99, 70, 5, NOW(), NOW());



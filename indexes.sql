-- SQL file for creating indexes to improve database performance.

-- This file contains CREATE INDEX statements for various tables in the database.
-- These indexes are suggested based on the analysis of the database schema and potential query patterns.

-- Indexes for the `orders` table
CREATE INDEX idx_orders_customer_id ON orders(customer_id);
CREATE INDEX idx_orders_affiliate_id ON orders(affiliate_id);
CREATE INDEX idx_orders_service_staff_id ON orders(service_staff_id);
CREATE INDEX idx_orders_time_slot_id ON orders(time_slot_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_date ON orders(date);

-- Indexes for the `services` table
CREATE INDEX idx_services_category_id ON services(category_id);

-- Indexes for the `staff` table
CREATE INDEX idx_staff_user_id ON staff(user_id);
CREATE INDEX idx_staff_supervisor_id ON staff(supervisor_id);

-- Indexes for the `order_services` table
CREATE INDEX idx_order_services_order_id ON order_services(order_id);
CREATE INDEX idx_order_services_service_id ON order_services(service_id);
CREATE INDEX idx_order_services_order_id_service_id ON order_services(order_id, service_id);

-- Indexes for the `reviews` table
CREATE INDEX idx_reviews_staff_id ON reviews(staff_id);
CREATE INDEX idx_reviews_service_id ON reviews(service_id);
CREATE INDEX idx_reviews_order_id ON reviews(order_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- Indexes for the `transactions` table
CREATE INDEX idx_transactions_user_id ON transactions(user_id);
CREATE INDEX idx_transactions_order_id ON transactions(order_id);
CREATE INDEX idx_transactions_status ON transactions(status);

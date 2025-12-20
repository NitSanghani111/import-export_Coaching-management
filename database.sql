-- Database Schema for Coaching Management System
-- Run this SQL in your MySQL/phpMyAdmin to set up the database

CREATE DATABASE IF NOT EXISTS coaching_management;
USE coaching_management;

-- Admin table for authentication
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (username: admin, password: admin123)
INSERT INTO admin (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@coaching.com');

-- Blogs table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Admin',
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(255),
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample blog posts
INSERT INTO blogs (title, slug, content, author) VALUES 
('Getting Started with Import/Export Business', 'getting-started-with-import-export-business', 'Starting an import/export business can be a lucrative venture. This comprehensive guide will walk you through the essential steps, documentation requirements, and best practices to help you succeed in international trade...', 'Admin'),
('Understanding International Trade Regulations', 'understanding-international-trade-regulations', 'International trade is governed by complex regulations that vary by country. Learn about customs procedures, tariffs, trade agreements, and compliance requirements to ensure smooth cross-border transactions...', 'Admin'),
('Top Export Markets for 2024', 'top-export-markets-2024', 'Discover the most promising export markets for the upcoming year. We analyze economic trends, trade policies, and market opportunities to help you make informed decisions for your export business...', 'Admin');

-- Sample events
INSERT INTO events (title, description, event_date, event_time, location) VALUES 
('Import/Export Workshop 2024', 'Join our comprehensive workshop covering all aspects of import/export business including documentation, logistics, and regulations.', '2024-02-15', '10:00:00', 'Online Webinar'),
('Trade Fair Exhibition', 'Network with international buyers and sellers at our annual trade fair. Showcase your products and explore new business opportunities.', '2024-03-20', '09:00:00', 'Convention Center, Mumbai'),
('Export Strategy Seminar', 'Learn advanced strategies for expanding your export business and entering new international markets.', '2024-04-10', '14:00:00', 'Online Webinar');

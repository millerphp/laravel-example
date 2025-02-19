# E-Commerce Application

A modern e-commerce platform built with Laravel and Vue.js, featuring a shopping cart system, product categories, and admin management.

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js (v16+) and npm
- SQLite
- Git

## Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd <project-directory>
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

For SQLite:
```bash
# Create SQLite database
touch database/database.sqlite


### 5. Run Migrations and Seeders

```bash
php artisan migrate:fresh --seed
```

### 6. Build Assets

```bash
npm run dev # for development
# or
npm run build # for production
```

### 7. Start the Server

```bash
php artisan serve
```

Visit `http://localhost:8000` to view the application.

## Architecture Overview

### Backend Components

#### Services Layer
- `DiscountService`: Handles product discount calculations and promotions
- Implements business logic separation for better maintainability

#### Models
- `Product`: Product entity with relationships to categories and cart items
- Implements cart functionality and product management

#### Controllers
- `CategoryController`: Handles product categorization
- Admin-specific controllers in the Admin namespace for backend management

#### Middleware
- `HandleInertiaRequests`: Manages Inertia.js requests and shared data

### Frontend Components

#### Vue.js Pages
- `Welcome.vue`: Main landing page
- `Products/Show.vue`: Product detail page with cart integration

#### State Management
- Uses Pinia store (`useCartStore.js`) for cart state management
- Implements persistent cart functionality

#### Event Listeners
- `MergeGuestCart`: Handles cart merging when guest users authenticate

## Design Decisions

1. **Cart Implementation**
   - Separate guest and authenticated user carts
   - Cart merging on user authentication
   - Persistent storage using local storage and database

2. **State Management**
   - Pinia chosen over Vuex for better TypeScript support and composition API compatibility
   - Centralized cart state management for consistent shopping experience

3. **Admin Interface**
   - Separate admin namespace for better organization
   - Dedicated controllers and views for admin functionality

4. **Service Layer**
   - Business logic abstraction in services
   - Improved maintainability and testability

## Future Improvements

1. **Performance Optimization**
   - Implement caching for product catalog
   - Add image optimization and lazy loading
   - Consider Redis for session storage

2. **Feature Additions**
   - User wishlist functionality
   - Advanced search with filters
   - Product reviews and ratings
   - Order tracking system

3. **Technical Improvements**
   - Add comprehensive test coverage
   - Implement API documentation
   - Set up CI/CD pipeline
   - Docker containerization

4. **Security Enhancements**
   - Two-factor authentication
   - Rate limiting for API endpoints
   - Enhanced admin access controls

5. **User Experience**
   - Real-time stock updates
   - Enhanced mobile responsiveness
   - Email notification system
   - Social media integration
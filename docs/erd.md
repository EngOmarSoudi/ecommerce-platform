# E-commerce Platform ERD (Entity Relationship Diagram)

## Core Entities and Relationships

### User Management
- **User** (id, name, email, password, phone, email_verified_at, created_at, updated_at)
  - Has many Roles (many-to-many)
  - Has many Orders
  - Has one Cart
  - Has many Payments
  - Has many Refunds

- **Role** (id, name, description, created_at, updated_at)
  - Has many Permissions (many-to-many)
  - Belongs to many Users (many-to-many)

- **Permission** (id, name, description, created_at, updated_at)
  - Belongs to many Roles (many-to-many)

### Product Catalog
- **Category** (id, parent_id, name, slug, description, is_active, sort_order, created_at, updated_at)
  - Has many Products
  - Has many Subcategories (self-referencing)
  - Belongs to many Promotions (many-to-many)

- **Brand** (id, name, slug, description, logo, is_active, created_at, updated_at)
  - Has many Products

- **Attribute** (id, name, slug, type, is_required, is_filterable, created_at, updated_at)
  - Has many AttributeValues

- **AttributeValue** (id, attribute_id, value, sort_order, created_at, updated_at)
  - Belongs to Attribute
  - Belongs to many SKUs (many-to-many)

- **Product** (id, category_id, brand_id, name, slug, sku_base, description, short_description, is_active, created_at, updated_at)
  - Belongs to Category
  - Belongs to Brand
  - Has one ProductDescription
  - Has many ProductMedia
  - Has many SKUs
  - Has many Reviews
  - Belongs to many Promotions (many-to-many)

- **ProductDescription** (id, product_id, description, meta_title, meta_description, meta_keywords, created_at, updated_at)
  - Belongs to Product

- **ProductMedia** (id, product_id, media_type, url, alt_text, sort_order, is_primary, created_at, updated_at)
  - Belongs to Product

### Inventory Management
- **Sku** (id, product_id, sku, name, description, price, cost_price, weight, dimensions, is_active, stock_quantity, low_stock_threshold, created_at, updated_at)
  - Belongs to Product
  - Has many StockLevels
  - Has many StockMovements
  - Has many CartItems
  - Has many OrderItems
  - Has many Units (many-to-many)
  - Has many AttributeValues (many-to-many)

- **Unit** (id, name, symbol, conversion_factor, is_base_unit, created_at, updated_at)
  - Has many SKUs (many-to-many)

### Warehouse & Stock Management
- **Warehouse** (id, name, code, address, city, state, country, postal_code, is_active, created_at, updated_at)
  - Has many StockLevels
  - Has many StockMovements

- **StockLevel** (id, warehouse_id, sku_id, quantity_on_hand, quantity_reserved, reorder_point, created_at, updated_at)
  - Belongs to Warehouse
  - Belongs to Sku

- **StockMovement** (id, warehouse_id, sku_id, movement_type, quantity, reference_type, reference_id, notes, created_at, updated_at)
  - Belongs to Warehouse
  - Belongs to Sku

### Shopping Cart
- **Cart** (id, user_id, session_id, created_at, updated_at)
  - Belongs to User
  - Has many CartItems

- **CartItem** (id, cart_id, sku_id, quantity, price, created_at, updated_at)
  - Belongs to Cart
  - Belongs to Sku

### Order Management
- **Order** (id, user_id, order_number, status, currency, subtotal, tax_amount, shipping_amount, discount_amount, total_amount, notes, created_at, updated_at)
  - Belongs to User
  - Has many OrderItems
  - Has many Payments
  - Has many Refunds
  - Has one Shipment
  - Has many OrderEvents

- **OrderItem** (id, order_id, sku_id, quantity, price, total_amount, created_at, updated_at)
  - Belongs to Order
  - Belongs to Sku

- **Shipment** (id, order_id, tracking_number, carrier, shipping_method, shipped_at, estimated_delivery_at, actual_delivery_at, status, created_at, updated_at)
  - Belongs to Order

- **OrderEvent** (id, order_id, event_type, description, created_at, updated_at)
  - Belongs to Order

### Payment Processing
- **Payment** (id, order_id, user_id, amount, currency, payment_method, transaction_id, status, paid_at, created_at, updated_at)
  - Belongs to Order
  - Belongs to User

- **Refund** (id, order_id, payment_id, user_id, amount, currency, reason, status, refunded_at, created_at, updated_at)
  - Belongs to Order
  - Belongs to Payment
  - Belongs to User

- **Transaction** (id, payment_id, transaction_id, amount, currency, status, gateway_response, created_at, updated_at)
  - Belongs to Payment

### Seller Management
- **Seller** (id, user_id, company_name, business_license_number, tax_id, status, created_at, updated_at)
  - Belongs to User
  - Has one SellerStore
  - Has many SellerDocuments
  - Has many Products

- **SellerStore** (id, seller_id, store_name, store_slug, description, logo_url, cover_image_url, is_active, created_at, updated_at)
  - Belongs to Seller

- **SellerDocument** (id, seller_id, document_type, file_path, is_verified, verified_at, created_at, updated_at)
  - Belongs to Seller

### Promotions & Marketing
- **Promotion** (id, name, description, promotion_type, discount_type, discount_value, start_date, end_date, is_active, usage_limit, usage_count, created_at, updated_at)
  - Has many Coupons
  - Belongs to many Categories (many-to-many)
  - Belongs to many Products (many-to-many)

- **Coupon** (id, promotion_id, code, usage_limit_per_user, usage_count, is_active, expires_at, created_at, updated_at)
  - Belongs to Promotion

### System & Audit
- **AuditLog** (id, user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent, created_at)
  - Optionally belongs to User
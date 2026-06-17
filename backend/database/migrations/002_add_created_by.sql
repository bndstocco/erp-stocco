-- Add created_by column to all user-owned tables
ALTER TABLE categories ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE products ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE customers ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE suppliers ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE sales ADD COLUMN created_by INT DEFAULT NULL AFTER notes;
ALTER TABLE purchases ADD COLUMN created_by INT DEFAULT NULL AFTER notes;
ALTER TABLE accounts ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE transactions ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE employees ADD COLUMN created_by INT DEFAULT NULL AFTER status;
ALTER TABLE attendance ADD COLUMN created_by INT DEFAULT NULL AFTER notes;
ALTER TABLE payroll ADD COLUMN created_by INT DEFAULT NULL AFTER notes;

-- Set created_by = 1 for existing records (admin user)
UPDATE categories SET created_by = 1 WHERE created_by IS NULL;
UPDATE products SET created_by = 1 WHERE created_by IS NULL;
UPDATE customers SET created_by = 1 WHERE created_by IS NULL;
UPDATE suppliers SET created_by = 1 WHERE created_by IS NULL;
UPDATE sales SET created_by = 1 WHERE created_by IS NULL;
UPDATE purchases SET created_by = 1 WHERE created_by IS NULL;
UPDATE accounts SET created_by = 1 WHERE created_by IS NULL;
UPDATE transactions SET created_by = 1 WHERE created_by IS NULL;
UPDATE employees SET created_by = 1 WHERE created_by IS NULL;
UPDATE attendance SET created_by = 1 WHERE created_by IS NULL;
UPDATE payroll SET created_by = 1 WHERE created_by IS NULL;

-- INSERT: Add a new food donation
INSERT INTO Food_Items (donor_id, food_name, quantity, unit, prepared_time, expiry_time)
VALUES (1, 'Chicken Biriyani', 30, 'plates', NOW(), DATE_ADD(NOW(), INTERVAL 6 HOUR));

-- SELECT: Get all food items
SELECT * FROM Food_Items;

-- SELECT: Get only available food (for receivers to browse)
SELECT * FROM Food_Items WHERE status = 'available';

-- SELECT: Get available food that has NOT expired yet
SELECT * FROM Food_Items
WHERE status = 'available' AND expiry_time > NOW();

-- SELECT: Get food items by a specific donor
SELECT * FROM Food_Items WHERE donor_id = 1;

-- SELECT: Get food items with donor name (JOIN)
SELECT f.food_id, f.food_name, f.quantity, f.unit,
       f.expiry_time, f.status, d.name AS donor_name, d.address
FROM Food_Items f
JOIN Donors d ON f.donor_id = d.donor_id
ORDER BY f.expiry_time ASC;

-- SELECT: Get available food with donor info (for receiver browse page)
SELECT f.food_id, f.food_name, f.quantity, f.unit,
       f.expiry_time, d.name AS donor_name, d.address AS pickup_location
FROM Food_Items f
JOIN Donors d ON f.donor_id = d.donor_id
WHERE f.status = 'available' AND f.expiry_time > NOW()
ORDER BY f.expiry_time ASC;

-- SELECT: Search food by name
SELECT * FROM Food_Items
WHERE food_name LIKE '%Biriyani%' AND status = 'available';

-- SELECT: Filter food by location (JOIN with Donors)
SELECT f.*, d.address AS location
FROM Food_Items f
JOIN Donors d ON f.donor_id = d.donor_id
WHERE d.address LIKE '%Narayanganj%' AND f.status = 'available';

-- SELECT: Get food items expiring within 2 hours (urgent)
SELECT * FROM Food_Items
WHERE status = 'available'
AND expiry_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR);

-- SELECT: Get expired food items
SELECT * FROM Food_Items WHERE expiry_time < NOW();

-- SELECT: Count food items by status
SELECT status, COUNT(*) AS total, SUM(quantity) AS total_quantity
FROM Food_Items
GROUP BY status;

-- SELECT: Total food donated (all time)
SELECT COUNT(*) AS total_donations, SUM(quantity) AS total_quantity
FROM Food_Items;

-- UPDATE: Change food item status to reserved
UPDATE Food_Items SET status = 'reserved' WHERE food_id = 1;

-- UPDATE: Mark food as distributed
UPDATE Food_Items SET status = 'distributed' WHERE food_id = 1;

-- UPDATE: Edit food details
UPDATE Food_Items
SET food_name = 'Mutton Biriyani', quantity = 40, expiry_time = DATE_ADD(NOW(), INTERVAL 8 HOUR)
WHERE food_id = 1;

-- DELETE: Remove a food item
DELETE FROM Food_Items WHERE food_id = 3;
-- SELECT: Get all donors
-- INSERT: Register a new donor profile
INSERT INTO Donors (user_id, name, contact, email, address, donor_type)
VALUES (2, 'Rahim Donor', '01711000001', 'rahim@gmail.com', 'Narayanganj, Dhaka', 'restaurant');
 
-- SELECT: Get all donors
SELECT * FROM Donors;
 
-- SELECT: Get donor by user_id (used after login)
SELECT * FROM Donors WHERE user_id = 2;
 
-- SELECT: Get all restaurant type donors
SELECT * FROM Donors WHERE donor_type = 'restaurant';
 
-- SELECT: Get donor full info with user account details (JOIN)
SELECT d.donor_id, d.name, d.contact, d.email, d.address,
       d.donor_type, u.role, u.created_at
FROM Donors d
JOIN Users u ON d.user_id = u.user_id;
 
-- SELECT: Count donors by type
SELECT donor_type, COUNT(*) AS total
FROM Donors
GROUP BY donor_type;
 
-- SELECT: Search donor by name or address
SELECT * FROM Donors
WHERE name LIKE '%Rahim%' OR address LIKE '%Dhaka%';
 
-- SELECT: Total food donated per donor
SELECT d.name AS donor_name,
       COUNT(f.food_id) AS total_items,
       SUM(f.quantity) AS total_quantity
FROM Donors d
JOIN Food_Items f ON d.donor_id = f.donor_id
GROUP BY d.donor_id
ORDER BY total_quantity DESC;
 
-- UPDATE: Update donor contact and address
UPDATE Donors SET contact = '01799999999', address = 'Gulshan, Dhaka'
WHERE donor_id = 1;
 
-- DELETE: Remove donor profile
DELETE FROM Donors WHERE donor_id = 3;

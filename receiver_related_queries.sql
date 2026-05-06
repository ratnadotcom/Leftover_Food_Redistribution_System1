-- INSERT: Register a new receiver profile
INSERT INTO Receivers (user_id, name, contact, email, address, receiver_type)
VALUES (4, 'NGO Bangladesh', '01811000001', 'ngo@bd.org', 'Mirpur, Dhaka', 'NGO');

-- SELECT: Get all receivers
SELECT * FROM Receivers;

-- SELECT: Get receiver by user_id (used after login)
SELECT * FROM Receivers WHERE user_id = 4;

-- SELECT: Get only NGO type receivers
SELECT * FROM Receivers WHERE receiver_type = 'NGO';

-- SELECT: Get individual receivers
SELECT * FROM Receivers WHERE receiver_type = 'individual';

-- SELECT: Receiver full info with user account (JOIN)
SELECT r.receiver_id, r.name, r.contact, r.email,
       r.address, r.receiver_type, u.created_at
FROM Receivers r
JOIN Users u ON r.user_id = u.user_id;

-- SELECT: Count receivers by type
SELECT receiver_type, COUNT(*) AS total
FROM Receivers
GROUP BY receiver_type;

-- SELECT: Search receiver by name or address
SELECT * FROM Receivers
WHERE name LIKE '%NGO%' OR address LIKE '%Dhaka%';

-- SELECT: Top receivers (most requests made)
SELECT r.name, COUNT(req.request_id) AS total_requests
FROM Receivers r
JOIN Requests req ON r.receiver_id = req.receiver_id
GROUP BY r.receiver_id
ORDER BY total_requests DESC;

-- UPDATE: Update receiver address and contact
UPDATE Receivers SET address = 'Banani, Dhaka', contact = '01888888888'
WHERE receiver_id = 1;

-- DELETE: Remove receiver profile
DELETE FROM Receivers WHERE receiver_id = 2;

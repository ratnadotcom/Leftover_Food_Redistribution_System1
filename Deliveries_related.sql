-- INSERT: Assign a delivery after request is approved
INSERT INTO Deliveries (request_id, delivery_person, contact, pickup_time, delivery_time)
VALUES (1, 'Raju Delivery', '01911000001', NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR));

-- SELECT: Get all deliveries
SELECT * FROM Deliveries;

-- SELECT: Get all active deliveries (not completed)
SELECT * FROM Deliveries WHERE status != 'completed';

-- SELECT: Get all completed deliveries
SELECT * FROM Deliveries WHERE status = 'completed';

-- SELECT: Full delivery info with food, donor, receiver details
SELECT del.delivery_id, del.delivery_person, del.contact,
       del.pickup_time, del.delivery_time, del.status AS delivery_status,
       f.food_name, f.quantity, f.unit,
       d.name AS donor_name, d.address AS pickup_address,
       r.name AS receiver_name, r.contact AS receiver_contact,
       r.address AS drop_address
FROM Deliveries del
JOIN Requests req   ON del.request_id = req.request_id
JOIN Food_Items f   ON req.food_id = f.food_id
JOIN Donors d       ON f.donor_id = d.donor_id
JOIN Receivers r    ON req.receiver_id = r.receiver_id
ORDER BY del.pickup_time ASC;

-- SELECT: Get delivery status for a specific request
SELECT del.*, req.status AS request_status
FROM Deliveries del
JOIN Requests req ON del.request_id = req.request_id
WHERE del.request_id = 1;

-- SELECT: Count deliveries by status
SELECT status, COUNT(*) AS total FROM Deliveries GROUP BY status;

-- SELECT: Deliveries assigned to a specific person
SELECT * FROM Deliveries WHERE delivery_person LIKE '%Raju%';

-- SELECT: Overdue deliveries (delivery_time passed but not completed)
SELECT * FROM Deliveries
WHERE delivery_time < NOW() AND status != 'completed';

-- UPDATE: Mark delivery as in progress
UPDATE Deliveries SET status = 'in_progress' WHERE delivery_id = 1;

-- UPDATE: Mark delivery as completed (then also update request + food)
UPDATE Deliveries SET status = 'completed', delivery_time = NOW()
WHERE delivery_id = 1;

UPDATE Requests    SET status = 'completed'    WHERE request_id = 1;
UPDATE Food_Items  SET status = 'distributed'  WHERE food_id = (
    SELECT food_id FROM Requests WHERE request_id = 1
);

-- UPDATE: Change delivery person
UPDATE Deliveries SET delivery_person = 'Kamal Driver', contact = '01922222222'
WHERE delivery_id = 2;

-- DELETE: Remove a delivery record
DELETE FROM Deliveries WHERE delivery_id = 4;

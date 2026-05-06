
INSERT INTO Requests (receiver_id, food_id, request_quantity)
VALUES (1, 2, 15);


SELECT * FROM Requests;


SELECT * FROM Requests WHERE status = 'pending';


SELECT * FROM Requests WHERE status = 'approved';

SELECT * FROM Requests WHERE receiver_id = 1;

SELECT req.request_id, req.request_quantity, req.request_time, req.status,
       r.name AS receiver_name, r.contact AS receiver_contact,
       f.food_name, f.quantity AS available_qty, f.unit,
       d.name AS donor_name, d.address AS pickup_location
FROM Requests req
JOIN Receivers r    ON req.receiver_id = r.receiver_id
JOIN Food_Items f   ON req.food_id = f.food_id
JOIN Donors d       ON f.donor_id = d.donor_id
ORDER BY req.request_time DESC;

SELECT req.request_id, req.request_quantity, req.status, req.request_time,
       r.name AS receiver_name, r.contact,
       f.food_name
FROM Requests req
JOIN Food_Items f ON req.food_id = f.food_id
JOIN Receivers r  ON req.receiver_id = r.receiver_id
WHERE f.donor_id = 1
ORDER BY req.request_time DESC;

SELECT req.request_id, f.food_name, d.name AS donor_name,
       req.request_quantity, req.status AS request_status,
       del.status AS delivery_status, del.delivery_person
FROM Requests req
JOIN Food_Items f    ON req.food_id = f.food_id
JOIN Donors d        ON f.donor_id = d.donor_id
LEFT JOIN Deliveries del ON del.request_id = req.request_id
WHERE req.receiver_id = 1
ORDER BY req.request_time DESC;


SELECT status, COUNT(*) AS total FROM Requests GROUP BY status;


SELECT * FROM Requests
WHERE receiver_id = 1 AND food_id = 2
AND status IN ('pending', 'approved');


UPDATE Requests SET status = 'approved' WHERE request_id = 1;


UPDATE Requests SET status = 'rejected' WHERE request_id = 2;

UPDATE Requests SET status = 'completed' WHERE request_id = 1;

DELETE FROM Requests WHERE request_id = 3 AND status = 'pending';

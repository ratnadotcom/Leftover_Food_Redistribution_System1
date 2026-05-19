CREATE TABLE requests (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    food_id     INT NOT NULL,
    receiver_id INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    message     TEXT,
    status      ENUM('pending','approved','rejected','delivered') DEFAULT 'pending',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (food_id)     REFERENCES food(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO requests (food_id, receiver_id, quantity, message, status) VALUES
(1, 4, 20, 'We need food for 20 families tonight.', 'approved'),
(3, 5, 5,  'For my neighbourhood.', 'pending');


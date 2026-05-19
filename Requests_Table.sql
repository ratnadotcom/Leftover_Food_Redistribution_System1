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

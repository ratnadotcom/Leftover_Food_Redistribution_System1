CREATE TABLE delivery (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    request_id      INT NOT NULL UNIQUE,
    delivery_person VARCHAR(100),
    contact         VARCHAR(15),
    delivery_status ENUM('assigned','in_progress','completed') DEFAULT 'assigned',
    notes           TEXT,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
);

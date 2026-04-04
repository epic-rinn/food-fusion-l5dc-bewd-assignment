CREATE TABLE IF NOT EXISTS cookbooks (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    description   TEXT NOT NULL COMMENT 'Step-by-step instructions, one step per line',
    photo         VARCHAR(255) NULL,
    country       VARCHAR(100) NOT NULL,
    cooking_type  VARCHAR(100) NOT NULL,
    tips          TEXT NULL,
    status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    total_likes   INT NOT NULL DEFAULT 0,
    user_id       INT NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_country (country),
    INDEX idx_cooking_type (cooking_type)
);

CREATE TABLE IF NOT EXISTS cookbook_comments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    cookbook_id  INT NOT NULL,
    user_id     INT NOT NULL,
    comment     TEXT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cookbook_id) REFERENCES cookbooks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_cookbook_id (cookbook_id)
);

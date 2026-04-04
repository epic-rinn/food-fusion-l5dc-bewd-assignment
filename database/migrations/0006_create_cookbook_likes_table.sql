CREATE TABLE IF NOT EXISTS cookbook_likes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    cookbook_id  INT NOT NULL,
    user_id     INT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cookbook_user (cookbook_id, user_id),
    FOREIGN KEY (cookbook_id) REFERENCES cookbooks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE
);

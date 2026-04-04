CREATE TABLE IF NOT EXISTS users (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)    NOT NULL,
    email           VARCHAR(150)    NOT NULL UNIQUE,
    password        VARCHAR(255)    NOT NULL,
    role            ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    failed_attempts INT             NOT NULL DEFAULT 0,
    locked_until    DATETIME        NULL,
    created_at      DATETIME        DEFAULT CURRENT_TIMESTAMP
);

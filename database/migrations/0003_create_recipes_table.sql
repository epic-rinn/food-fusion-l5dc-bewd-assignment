CREATE TABLE IF NOT EXISTS recipes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255)  NOT NULL,
    description     TEXT          NULL,
    category        VARCHAR(100)  NULL,
    prep_time       INT           NULL COMMENT 'minutes',
    cook_time       INT           NULL COMMENT 'minutes',
    servings        INT           NULL,
    cooking_method  VARCHAR(100)  NULL,
    photo           VARCHAR(255)  NULL COMMENT 'filename in uploads/recipes/',
    created_by      INT           NOT NULL,
    created_at      DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

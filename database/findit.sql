CREATE DATABASE IF NOT EXISTS findit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE findit;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    status ENUM('active','disabled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    report_type ENUM('lost','found') NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    colour VARCHAR(80) NOT NULL,
    category VARCHAR(80) NOT NULL,
    location VARCHAR(150) NOT NULL,
    incident_date DATE NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    status ENUM('open','claimed','returned','closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_items_type_status (report_type, status),
    INDEX idx_items_date (incident_date),
    INDEX idx_items_category (category)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_matches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_item_id INT UNSIGNED NOT NULL,
    candidate_item_id INT UNSIGNED NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    components_json JSON DEFAULT NULL,
    engine VARCHAR(40) NOT NULL DEFAULT 'python',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_matches_source FOREIGN KEY (source_item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_matches_candidate FOREIGN KEY (candidate_item_id) REFERENCES items(id) ON DELETE CASCADE,
    UNIQUE KEY uq_match_pair (source_item_id, candidate_item_id),
    INDEX idx_matches_source_score (source_item_id, score)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS claims (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    claimant_id INT UNSIGNED NOT NULL,
    lost_item_id INT UNSIGNED NOT NULL,
    found_item_id INT UNSIGNED NOT NULL,
    evidence TEXT NOT NULL,
    status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    reviewed_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_claim_claimant FOREIGN KEY (claimant_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_claim_lost FOREIGN KEY (lost_item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_claim_found FOREIGN KEY (found_item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_claim_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_claim_status (status),
    INDEX idx_claimant (claimant_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    message VARCHAR(255) NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notification_user_read (user_id, is_read)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_created (created_at),
    INDEX idx_audit_action (action)
) ENGINE=InnoDB;

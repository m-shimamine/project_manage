-- タスクテーブル作成SQL
-- このSQLファイルをデータベースで直接実行してください

-- タスクテーブルを作成
CREATE TABLE IF NOT EXISTS `tasks` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT(11) UNSIGNED NOT NULL COMMENT 'プロジェクトID',
    `parent_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT '親タスクID（サブタスク用）',
    `process_id` INT(11) UNSIGNED NOT NULL COMMENT '工程ID',
    `screen_name` VARCHAR(100) NULL DEFAULT NULL COMMENT '画面名',
    `task_name` VARCHAR(255) NOT NULL COMMENT 'タスク名',
    `sort_order` INT(11) NOT NULL DEFAULT 0 COMMENT '表示順',
    `level` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '階層レベル（1:親タスク, 2:サブタスク）',
    `assignee_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '担当者ID',
    `status` ENUM('not_started', 'in_progress', 'completed', 'on_hold') NOT NULL DEFAULT 'not_started' COMMENT 'ステータス',
    `sales_man_days` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '営業工数（人日）',
    `planned_man_days` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '予定工数（人日）',
    `planned_start_date` DATE NULL DEFAULT NULL COMMENT '予定開始日',
    `planned_end_date` DATE NULL DEFAULT NULL COMMENT '予定終了日',
    `planned_cost` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '予定原価（円）',
    `actual_man_days` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '実績工数（人日）',
    `actual_start_date` DATE NULL DEFAULT NULL COMMENT '実績開始日',
    `actual_end_date` DATE NULL DEFAULT NULL COMMENT '実績終了日',
    `actual_cost` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '出来高・実績原価（円）',
    `progress` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '進捗率（0-100）',
    `delay_days` INT(11) NOT NULL DEFAULT 0 COMMENT '遅延日数（正:遅れ、負:先行）',
    `description` TEXT NULL DEFAULT NULL COMMENT '説明・備考',
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    `deleted_at` DATETIME NULL DEFAULT NULL COMMENT '削除日時（論理削除）',
    PRIMARY KEY (`id`),
    INDEX `idx_tasks_project_id` (`project_id`),
    INDEX `idx_tasks_parent_id` (`parent_id`),
    INDEX `idx_tasks_process_id` (`process_id`),
    INDEX `idx_tasks_assignee_id` (`assignee_id`),
    INDEX `idx_tasks_status` (`status`),
    INDEX `idx_tasks_planned_dates` (`planned_start_date`, `planned_end_date`),
    CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `tasks_process_id_foreign` FOREIGN KEY (`process_id`) REFERENCES `process_masters` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `tasks_assignee_id_foreign` FOREIGN KEY (`assignee_id`) REFERENCES `members` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 自己参照の外部キーを追加
ALTER TABLE `tasks` ADD CONSTRAINT `fk_tasks_parent` FOREIGN KEY (`parent_id`) REFERENCES `tasks`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- タスク変更履歴テーブルを作成
CREATE TABLE IF NOT EXISTS `task_history` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `task_id` BIGINT(20) UNSIGNED NOT NULL COMMENT 'タスクID',
    `changed_by` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '変更者ID',
    `field_name` VARCHAR(50) NOT NULL COMMENT '変更フィールド名',
    `old_value` TEXT NULL DEFAULT NULL COMMENT '変更前の値',
    `new_value` TEXT NULL DEFAULT NULL COMMENT '変更後の値',
    `created_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_task_history_task_id` (`task_id`),
    INDEX `idx_task_history_changed_by` (`changed_by`),
    INDEX `idx_task_history_created_at` (`created_at`),
    CONSTRAINT `task_history_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `task_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `members` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- マイグレーション履歴に追加（CodeIgniter用）
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
('2026-01-18-000002', 'App\\Database\\Migrations\\CreateTasksTable', 'default', 'App', UNIX_TIMESTAMP(), (SELECT MAX(batch) + 1 FROM (SELECT * FROM migrations) AS m)),
('2026-01-18-000003', 'App\\Database\\Migrations\\CreateTaskHistoryTable', 'default', 'App', UNIX_TIMESTAMP(), (SELECT MAX(batch) + 1 FROM (SELECT * FROM migrations) AS m2));

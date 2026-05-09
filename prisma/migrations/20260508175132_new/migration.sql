-- AlterTable
ALTER TABLE `links` ADD COLUMN `category_id` INTEGER NULL,
    ADD COLUMN `last_click_at` DATETIME(3) NULL,
    ADD COLUMN `views` INTEGER NOT NULL DEFAULT 0;

-- CreateTable
CREATE TABLE `link_categories` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `icon` VARCHAR(191) NULL DEFAULT 'folder',
    `color` VARCHAR(191) NULL DEFAULT '#4f46e5',
    `position` INTEGER NOT NULL DEFAULT 0,
    `is_active` BOOLEAN NOT NULL DEFAULT true,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated_at` DATETIME(3) NOT NULL,

    UNIQUE INDEX `link_categories_user_id_name_key`(`user_id`, `name`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `link_categories` ADD CONSTRAINT `link_categories_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `links` ADD CONSTRAINT `links_category_id_fkey` FOREIGN KEY (`category_id`) REFERENCES `link_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AlterTable
ALTER TABLE `profiles` ADD COLUMN `default_theme` VARCHAR(191) NULL,
    ADD COLUMN `footer` JSON NULL,
    ADD COLUMN `integrations` JSON NULL,
    ADD COLUMN `pages` JSON NULL;

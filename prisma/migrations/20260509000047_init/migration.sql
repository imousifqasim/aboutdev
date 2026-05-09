-- AlterTable
ALTER TABLE `profiles` ADD COLUMN `advanced_seo` JSON NULL,
    ADD COLUMN `builder_settings` JSON NULL,
    ADD COLUMN `focus_keyword` VARCHAR(191) NULL,
    ADD COLUMN `og_description` TEXT NULL,
    ADD COLUMN `og_title` VARCHAR(191) NULL,
    ADD COLUMN `portfolio_data` JSON NULL,
    ADD COLUMN `seo_settings` JSON NULL;

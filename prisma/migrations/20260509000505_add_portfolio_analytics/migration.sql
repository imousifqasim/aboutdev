-- AlterTable
ALTER TABLE `analytics` ADD COLUMN `portfolio_clicks` INTEGER NOT NULL DEFAULT 0,
    ADD COLUMN `portfolio_views` INTEGER NOT NULL DEFAULT 0;

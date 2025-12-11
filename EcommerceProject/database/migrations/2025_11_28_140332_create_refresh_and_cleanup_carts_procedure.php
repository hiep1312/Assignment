<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<SQL
        CREATE PROCEDURE refresh_and_cleanup_carts(
            IN p_extend_value INT,
            IN p_extend_unit VARCHAR(10)
        )
        BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;

            START TRANSACTION;

            DELETE ci FROM cart_items ci
            INNER JOIN product_variants pv ON ci.product_variant_id = pv.id
            INNER JOIN product_variant_inventories pvi ON pv.id = pvi.variant_id
            WHERE pv.status = 0 OR pv.deleted_at IS NOT NULL OR pvi.stock <= 0;

            UPDATE cart_items ci
            INNER JOIN product_variants pv ON ci.product_variant_id = pv.id
            INNER JOIN product_variant_inventories pvi ON pv.id = pvi.variant_id
            SET ci.price = COALESCE(pv.discount, pv.price),
                ci.quantity = LEAST(pvi.stock, ci.quantity),
                ci.updated_at = CURRENT_TIMESTAMP
            WHERE pv.status = 1 AND pv.deleted_at IS NULL;

            UPDATE carts
            SET status = 1,
                expires_at = CASE p_extend_unit
                    WHEN 'SECOND' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value SECOND)
                    WHEN 'MINUTE' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value MINUTE)
                    WHEN 'HOUR' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value HOUR)
                    WHEN 'DAY' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value DAY)
                    WHEN 'WEEK' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value WEEK)
                    WHEN 'MONTH' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value MONTH)
                    WHEN 'YEAR' THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value YEAR)
                    ELSE DATE_ADD(CURRENT_TIMESTAMP, INTERVAL p_extend_value DAY)
                END,
                updated_at = CURRENT_TIMESTAMP
                WHERE status = 0 AND expires_at <= CURRENT_TIMESTAMP AND user_id IS NOT NULL;

            DELETE FROM carts
            WHERE status = 0 AND expires_at <= CURRENT_TIMESTAMP AND user_id IS NULL;

            COMMIT;
        END;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<SQL
            DROP PROCEDURE IF EXISTS refresh_and_cleanup_carts
        SQL);
    }
};

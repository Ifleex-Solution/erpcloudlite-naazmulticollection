-- Renames "Inventory Request" to "Inventory Transaction Request" in the
-- sidebar (these two phrases are pulled via display() from this table —
-- the phrase/slug stays the same, only the displayed English text changes).
-- Safe to re-run: plain UPDATEs, idempotent by nature.

UPDATE language SET english = 'Inventory Transaction Request'
WHERE phrase = 'new_inventory_request';

UPDATE language SET english = 'Manage Inventory Transaction Request'
WHERE phrase = 'manage_inventory_request';

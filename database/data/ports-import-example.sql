-- Ports table bulk import template
-- Run migration first: php artisan migrate
--
-- Airports: use type = 'airport', iata_code + icao_code
-- Seaports: use type = 'seaport', un_locode (5-char UN/LOCODE)
--
-- Example airport rows (matches your sample):

INSERT INTO ports
(type, iata_code, icao_code, un_locode, port_name, city, country_name, country_code, flag, is_active, created_at, updated_at)
VALUES
('airport', 'DEL', 'VIDP', NULL, 'Indira Gandhi International Airport', 'Delhi', 'India', 'IN', '🇮🇳', 1, NOW(), NOW()),
('airport', 'BOM', 'VABB', NULL, 'Chhatrapati Shivaji Maharaj International Airport', 'Mumbai', 'India', 'IN', '🇮🇳', 1, NOW(), NOW()),
('airport', 'LHR', 'EGLL', NULL, 'Heathrow Airport', 'London', 'United Kingdom', 'GB', '🇬🇧', 1, NOW(), NOW()),
('airport', 'JFK', 'KJFK', NULL, 'John F. Kennedy International Airport', 'New York', 'United States', 'US', '🇺🇸', 1, NOW(), NOW()),
('airport', 'DXB', 'OMDB', NULL, 'Dubai International Airport', 'Dubai', 'United Arab Emirates', 'AE', '🇦🇪', 1, NOW(), NOW());

-- Example seaport rows:

INSERT INTO ports
(type, iata_code, icao_code, un_locode, port_name, city, country_name, country_code, flag, is_active, created_at, updated_at)
VALUES
('seaport', NULL, NULL, 'INNSA', 'Jawaharlal Nehru Port (Nhava Sheva)', 'Mumbai', 'India', 'IN', '🇮🇳', 1, NOW(), NOW()),
('seaport', NULL, NULL, 'SGSIN', 'Port of Singapore', 'Singapore', 'Singapore', 'SG', '🇸🇬', 1, NOW(), NOW()),
('seaport', NULL, NULL, 'NLRTM', 'Port of Rotterdam', 'Rotterdam', 'Netherlands', 'NL', '🇳🇱', 1, NOW(), NOW());

-- Bulk data sources for all countries:
-- Airports: https://ourairports.com/data/ (airports.csv)
-- Seaports: UN/LOCODE datasets (e.g. UNECE UN/LOCODE code list by country)
--
-- Optional: link to countries table
-- UPDATE ports p
-- JOIN countries c ON c.iso_code = p.country_code
-- SET p.country_id = c.id;

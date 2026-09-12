CREATE TABLE IF NOT EXISTS medications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL CHECK (length(trim(name)) > 0),
    normalized_name TEXT NOT NULL UNIQUE CHECK (length(trim(normalized_name)) > 0),
    generic_name TEXT NOT NULL CHECK (length(trim(generic_name)) > 0),
    presentation TEXT NOT NULL CHECK (length(trim(presentation)) > 0),
    concentration TEXT NOT NULL CHECK (length(trim(concentration)) > 0),
    status TEXT NOT NULL CHECK (status IN ('ACTIVE', 'INACTIVE')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)

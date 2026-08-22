INSERT OR IGNORE INTO medications
    (name, normalized_name, generic_name, presentation, concentration, status)
VALUES
    (:name, :normalized_name, :generic_name, :presentation, :concentration, :status)

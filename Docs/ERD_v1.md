# Entity Relationship Diagram (ERD)

## Entities
- students
- student_categories
- fees
- payments
- payment_items
- journals
- journal_lines
- users
- roles

## Relations
students -> payments  
payments -> payment_items  
payments -> journals  
journals -> journal_lines  
users -> payments  

## Notes
Support partial payment & multi year.

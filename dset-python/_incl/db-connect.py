from sqlalchemy import create_engine

# Database configuration for SQLite
db_name = 'dset-app-sqlite-v2.db'

# SQLAlchemy engine creation for SQLite
engine = create_engine(f'sqlite:///{db_name}')

# Test connection
try:
    with engine.connect() as connection:
        print("Database connection successful")
except Exception as e:
    print(f"Error connecting to database: {e}")


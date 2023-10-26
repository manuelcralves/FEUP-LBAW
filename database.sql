DROP TABLE IF EXISTS authenticated_user CASCADE;
DROP TABLE IF EXISTS address CASCADE;
DROP TABLE IF EXISTS auction CASCADE;
DROP TABLE IF EXISTS item CASCADE;
DROP TABLE IF EXISTS bid CASCADE;
DROP TABLE IF EXISTS transaction CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS review CASCADE;
DROP TABLE IF EXISTS report_auction CASCADE;
DROP TABLE IF EXISTS following CASCADE;

DROP TYPE IF EXISTS AUCTION_STATUS CASCADE;
DROP TYPE IF EXISTS ITEM_CONDITION CASCADE;
DROP TYPE IF EXISTS ROLES CASCADE;
DROP TYPE IF EXISTS NOTIFICATION_TYPE CASCADE;
DROP DOMAIN IF EXISTS EURO;
DROP DOMAIN IF EXISTS TODAY;
---------------------------Types--------------------
CREATE TYPE AUCTION_STATUS AS ENUM ('ACTIVE', 'CLOSED', 'CANCELLED');
CREATE TYPE ITEM_CONDITION AS ENUM ('NEW', 'LIKE NEW', 'EXCELLENT', 'GOOD', 'USED');
CREATE TYPE ROLES AS ENUM ('USER', 'ADMIN');
CREATE TYPE NOTIFICATION_TYPE AS ENUM ('RATING', 'AUCTION WON', 'AUCTION OUTBID', 'AUCTION SOLD', 'REPORT', 'AUCTION END');
CREATE DOMAIN EURO AS NUMERIC(20, 2) NOT NULL;
CREATE DOMAIN TODAY AS TIMESTAMP DEFAULT CURRENT_TIMESTAMP CHECK (VALUE <= CURRENT_TIMESTAMP);



------------------Tables---------------------------

CREATE TABLE authenticated_user (
    id SERIAL PRIMARY KEY,
    username TEXT UNIQUE NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    rating FLOAT,
    picture TEXT DEFAULT 'default.jpg',
    balance EURO,
    is_blocked BOOLEAN NOT NULL DEFAULT FALSE,
    role ROLES NOT NULL DEFAULT 'USER'
);

CREATE TABLE address (
    id SERIAL PRIMARY KEY,
    street TEXT NOT NULL,
    postal_code TEXT NOT NULL,
    city TEXT NOT NULL,
    country TEXT NOT NULL,
    "user" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE
);

CREATE TABLE item (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    category TEXT,
    brand TEXT,
    color TEXT,
    picture TEXT NOT NULL,
    condition ITEM_CONDITION NOT NULL
);

CREATE TABLE auction (
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    start_date TIMESTAMP DEFAULT now(),
    end_date TIMESTAMP NOT NULL,
    starting_price EURO NOT NULL,
    current_price EURO NOT NULL,
    status AUCTION_STATUS NOT NULL,
    "owner" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE,
    "item" INTEGER NOT NULL REFERENCES item(id) ON DELETE CASCADE
);

CREATE TABLE bid (
    id SERIAL PRIMARY KEY,
    value EURO NOT NULL,
    creation_date TIMESTAMP DEFAULT now(),
    "user" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE,
    "auction" INTEGER NOT NULL REFERENCES auction(id) ON DELETE CASCADE
);

CREATE TABLE transaction (
    id SERIAL PRIMARY KEY,
    value EURO NOT NULL,
    transaction_date TIMESTAMP NOT NULL,
    description TEXT,
    "user" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE
);

CREATE TABLE notification (
    id SERIAL PRIMARY KEY,
    message TEXT NOT NULL,
    type NOTIFICATION_TYPE NOT NULL,
    creation_date TIMESTAMP DEFAULT now(),
    read BOOLEAN DEFAULT FALSE,
    "user" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE
);

CREATE TABLE review (
    id SERIAL PRIMARY KEY,
    rating INTEGER NOT NULL CHECK (rating > 0 AND rating <= 5),
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    date TIMESTAMP DEFAULT now(),
    "reviewer" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE,
    "reviewed" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE,
    "auction" INTEGER NOT NULL REFERENCES auction(id) ON DELETE CASCADE
);

CREATE TABLE report_auction (
    id SERIAL PRIMARY KEY,
    reason TEXT NOT NULL,
    creation_date TIMESTAMP DEFAULT now(),
    "user" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE,
    "auction" INTEGER NOT NULL REFERENCES auction(id) ON DELETE CASCADE
);

CREATE TABLE following (
    PRIMARY KEY ("user", auction),
    auction INTEGER NOT NULL REFERENCES auction(id) ON DELETE CASCADE,
    notifications BOOLEAN DEFAULT TRUE,
    start_date TIMESTAMP DEFAULT now(),
    "user" INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE
);
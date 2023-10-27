DROP SCHEMA IF EXISTS lbaw23152 CASCADE;

CREATE SCHEMA IF NOT EXISTS lbaw23152;

SET search_path TO lbaw23152;

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
    balance EURO DEFAULT 0,
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

------------------Indexes---------------------------

CREATE INDEX active_auctions ON auction USING hash (status) WHERE status='ACTIVE';

CREATE INDEX bid_on_auction ON bid USING hash (auction);

CREATE INDEX notification_users ON notification USING hash ("user");

---FTS

ALTER TABLE auction ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION auction_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors := (
            setweight(to_tsvector('english', NEW.title), 'A') ||
            setweight(to_tsvector('english', NEW.description), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.description <> OLD.description OR NEW.title <> OLD.title) THEN
            NEW.tsvectors := (
                setweight(to_tsvector('english', NEW.title), 'A') ||
                setweight(to_tsvector('english', NEW.description), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER auction_search_update
    BEFORE INSERT OR UPDATE ON auction
    FOR EACH ROW
    EXECUTE PROCEDURE auction_search_update();

CREATE INDEX search_idx ON auction USING GIN (tsvectors);

---------------Triggers-----------------------------
--t01
CREATE OR REPLACE FUNCTION update_current_price_function() RETURNS TRIGGER AS
$BODY$
BEGIN
   -- Check if the new bid value is at least 5% higher than the current price of the auction
   IF NEW.value >= (SELECT current_price * 1.05 FROM auction WHERE id = NEW.auction) THEN
       -- Update the current price
       UPDATE auction
       SET current_price = NEW.value
       WHERE id = NEW.auction;
   ELSE
       RAISE EXCEPTION 'New bid must be at least 5%% higher than the current bid';
   END IF;
   RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER update_current_price
    AFTER INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE update_current_price_function();
    
--t02
CREATE OR REPLACE FUNCTION check_previous_bid_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    previous_bid_exists BOOLEAN;
BEGIN
    SELECT EXISTS (
        SELECT 1
        FROM bid
        WHERE auction = NEW.auction AND user = NEW.user
        ORDER BY value DESC LIMIT 1
    ) INTO previous_bid_exists;
    
    IF previous_bid_exists THEN
        RAISE EXCEPTION 'You cannot bid again if you are winning the auction.';
    END IF;

    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER check_previous_bid
    BEFORE INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE check_previous_bid_function();
    
--t03
CREATE OR REPLACE FUNCTION extend_auction_end_time_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF (SELECT end_date FROM auction WHERE id = NEW.auction) - INTERVAL '15 minutes' <= NOW() THEN
        UPDATE auction
        SET end_date = end_date + INTERVAL '30 minutes'
        WHERE id = NEW.auction;
    END IF;
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER extend_auction_end_time
    AFTER INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE extend_auction_end_time_function();
    
--t04
CREATE OR REPLACE FUNCTION check_auction_duration_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    auction_duration INTERVAL;
BEGIN
    -- Calculate the duration of the auction
    auction_duration := NEW.end_date - NEW.start_date;

    -- Check if the auction duration is within the required limits
    IF auction_duration <= INTERVAL '1 day' OR auction_duration >= INTERVAL '30 days' THEN
        RAISE EXCEPTION 'Auction duration must be more than 1 day and less than 30 days';
    END IF;

    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER check_auction_duration
    BEFORE INSERT OR UPDATE ON auction
    FOR EACH ROW
    EXECUTE PROCEDURE check_auction_duration_function();
    
--t05
CREATE OR REPLACE FUNCTION prevent_self_bidding_function() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF EXISTS (
       SELECT 1
       FROM auction
       WHERE auction.id = NEW.auction AND auction.owner = NEW.user
   ) THEN
       RAISE EXCEPTION 'Owners are not allowed to bid on their own auctions.';
   END IF;
   RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER prevent_self_bidding
    BEFORE INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE prevent_self_bidding_function();

--t06
CREATE OR REPLACE FUNCTION rate_won_auctions_function() RETURNS TRIGGER AS
$BODY$
BEGIN
        IF (SELECT COUNT(id) FROM auction WHERE status =  'CLOSED' AND highestBidder = NEW.reviewer AND owner = NEW.reviewed) <= (SELECT COUNT(id) FROM review WHERE reviewer = NEW.reviewer AND reviewed = NEW.reviewed) THEN
            RAISE EXCEPTION 'To rate an auctioneer, a user has to win an auction of theirs';   
        END IF;
        RETURN NEW;       
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER rate_won_auctions
    BEFORE INSERT ON review
    FOR EACH ROW
    EXECUTE PROCEDURE rate_won_auctions_function();
    
--t07
CREATE OR REPLACE FUNCTION check_starting_price_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.starting_price <= 0 THEN
        RAISE EXCEPTION 'Starting price must be higher than 0.';
    END IF;
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER check_starting_price
    BEFORE INSERT OR UPDATE OF starting_price ON auction
    FOR EACH ROW
    EXECUTE PROCEDURE check_starting_price_function();

--t08
CREATE OR REPLACE FUNCTION check_wallet_balance_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.balance < 0 THEN
        RAISE EXCEPTION 'Wallet balance cannot be negative.';
    END IF;
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER check_wallet_balance
    BEFORE UPDATE OF balance ON authenticated_user
    FOR EACH ROW
    EXECUTE PROCEDURE check_wallet_balance_function();
    
--t09
CREATE OR REPLACE FUNCTION check_and_update_user_balance_and_create_transaction_function() RETURNS TRIGGER AS
$BODY$
DECLARE 
    user_balance EURO;
BEGIN
    -- Get the user balance
    SELECT balance INTO user_balance
    FROM authenticated_user
    WHERE id = NEW."user";
    
    -- Check if the user has enough balance to make the bid
    IF user_balance < NEW.value THEN
        RAISE EXCEPTION 'Insufficient funds to place the bid.';
    END IF;
    
    -- If the bid is valid, subtract the value from the user’s balance
    UPDATE authenticated_user
    SET balance = balance - NEW.value
    WHERE id = NEW."user";
    
    -- Create a transaction record
    INSERT INTO transaction (value, transaction_date, description, "user")
    VALUES (-NEW.value, NOW(), 'Bid on auction (' || NEW.auction || ')', NEW."user");
    
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_and_update_user_balance_and_create_transaction
    BEFORE INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE check_and_update_user_balance_and_create_transaction_function();

    
--t10
CREATE OR REPLACE FUNCTION update_user_rating_function() RETURNS TRIGGER AS
$BODY$
DECLARE 
    new_avg_rating FLOAT;
BEGIN
    -- Calculate the new rating
    SELECT AVG(rating) INTO new_avg_rating
    FROM review
    WHERE reviewed = NEW.reviewed;
    
    -- Update the rating
    UPDATE authenticated_user
    SET rating = new_avg_rating
    WHERE id = NEW.reviewed;
    
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER update_user_rating
    AFTER INSERT OR UPDATE OF rating ON review
    FOR EACH ROW
    EXECUTE PROCEDURE update_user_rating_function();
    
--t11
CREATE OR REPLACE FUNCTION update_deleted_user_info_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    each_row RECORD;  -- Declare a record variable here
BEGIN
    -- Anonymizing bids by the user to be deleted
    UPDATE bid
    SET "user" = NULL
    WHERE "user" = OLD.id;

    -- Anonymizing reviews made by the user to be deleted
    UPDATE review
    SET reviewer = NULL
    WHERE reviewer = OLD.id;

    -- Adjusting auctions where the user to be deleted was the highest bidder
    FOR each_row IN (SELECT a.id AS auction_id
                     FROM auction a
                     JOIN bid b ON a.id = b.auction
                     WHERE b."user" = OLD.id AND a.status = 'ACTIVE'
                     ORDER BY b.value DESC, b.creation_date DESC LIMIT 1) 
    LOOP
        UPDATE auction
        SET current_price = (SELECT value
                             FROM bid
                             WHERE auction = each_row.auction_id AND "user" IS NOT NULL
                             ORDER BY value DESC LIMIT 1)
        WHERE id = each_row.auction_id;
    END LOOP;

    RETURN OLD;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_deleted_user_info
    BEFORE DELETE ON authenticated_user
    FOR EACH ROW
    EXECUTE FUNCTION update_deleted_user_info_function();

    
--t12
CREATE OR REPLACE FUNCTION min_bid_delete_auction_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    bid_count INT;
BEGIN
    -- Checking the number of bids for the auction to be deleted
    SELECT COUNT(*)
    INTO bid_count
    FROM bid
    WHERE bid.auction = OLD.id;

    IF bid_count > 0 THEN
        RAISE EXCEPTION 'An auction can’t be deleted if it has more than 0 bids';
    END IF;

    RETURN OLD;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER min_bid_delete_auction
    BEFORE DELETE ON auction
    FOR EACH ROW
    EXECUTE FUNCTION min_bid_delete_auction_function();
    
--t13
CREATE OR REPLACE FUNCTION auction_end_status_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    -- Change status of auctions that have reached end date
    UPDATE auction
    SET status = 'CLOSED'
    WHERE end_date <= NOW() AND status = 'ACTIVE';
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER auction_end_status_before_update
BEFORE UPDATE ON auction
    FOR EACH ROW
    EXECUTE FUNCTION auction_end_status_function();

CREATE TRIGGER auction_end_status_before_insert_or_update
    BEFORE INSERT OR UPDATE ON bid
    FOR EACH ROW
    EXECUTE FUNCTION auction_end_status_function();
    
--t14
CREATE OR REPLACE FUNCTION complete_auction_transaction() RETURNS TRIGGER AS
$BODY$
DECLARE
    final_bid EURO;
    winning_bidder INTEGER;
BEGIN
    IF NEW.status = 'CLOSED' THEN
        -- Get the highest bid and bidder for the auction
        SELECT value, "user" INTO final_bid, winning_bidder
        FROM bid
        WHERE auction = NEW.id
        ORDER BY value DESC LIMIT 1;
        
        -- Notify the owner
        INSERT INTO notification (message, type, creation_date, "user")
        VALUES (
            'Your auction "' || NEW.title || '" was sold for ' || final_bid || ' EURO.',
            'AUCTION SOLD',
            NOW(),
            NEW.owner
        );
        
        -- Update the owner's balance and create a transaction record
        UPDATE authenticated_user
        SET balance = balance + final_bid
        WHERE id = NEW.owner;
        
        INSERT INTO transaction (value, transaction_date, description, "user")
        VALUES (
            final_bid,
            NOW(),
            'Received funds from auction ID: ' || NEW.id,
            NEW.owner
        );
    END IF;
    
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER complete_auction_transaction_trigger
    AFTER UPDATE OF status ON auction
    FOR EACH ROW
    WHEN (NEW.status = 'CLOSED')
    EXECUTE FUNCTION complete_auction_transaction();
    
--t15
CREATE OR REPLACE FUNCTION auto_follow_auction_function() RETURNS TRIGGER AS
$BODY$
BEGIN
   -- Check if the user follows the auction
   IF NOT EXISTS (
       SELECT 1
       FROM following
       WHERE following."user" = NEW."user" AND following.auction = NEW.auction
   ) THEN
       -- If not, inserts new record in following table
       INSERT INTO following ("user", auction, start_date)
       VALUES (NEW."user", NEW.auction, NOW());
   END IF;
   RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER auto_follow_auction
    AFTER INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE auto_follow_auction_function();

--t16
CREATE OR REPLACE FUNCTION notify_owner_and_followers_new_bid_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    follower RECORD;
BEGIN
    -- Notify the auction owner
    INSERT INTO notification (message, type, creation_date, "user")
    SELECT
        'A new bid has been placed on your auction: "' || auction.title || '"',
        'AUCTION BID',
        NOW(),
        auction.owner
    FROM
        auction
    WHERE
        auction.id = NEW.auction;
    
    -- Notify the followers
    FOR follower IN (SELECT "user" FROM following WHERE auction = NEW.auction)
    LOOP
        INSERT INTO notification (message, type, creation_date, "user")
        VALUES ('A new bid has been placed on the auction: "' || (SELECT title FROM auction WHERE id = NEW.auction) || '"', 
                'AUCTION BID', NOW(), follower."user");
    END LOOP;
    
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER notify_owner_and_followers_new_bid
    AFTER INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE notify_owner_and_followers_new_bid_function();

    
--t17
CREATE OR REPLACE FUNCTION notify_and_refund_outbid_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    outbid_user INT;
    auction_title TEXT;
    outbid_value EURO;
BEGIN
    -- Find the user who has the second highest bid
    SELECT bid."user", bid.value INTO outbid_user, outbid_value
    FROM bid
    WHERE bid.auction = NEW.auction AND bid."user" != NEW."user"
    ORDER BY bid.value DESC
    LIMIT 1 OFFSET 1;
    
    -- Get the title of the auction
    SELECT auction.title INTO auction_title
    FROM auction
    WHERE auction.id = NEW.auction;
    
    -- Refund the outbid user and insert a notification for them
    IF outbid_user IS NOT NULL THEN
        -- Increase the outbid user's balance
        UPDATE authenticated_user
        SET balance = balance + outbid_value
        WHERE id = outbid_user;

        -- Create a transaction record for the refund
        INSERT INTO transaction (value, transaction_date, description, "user")
        VALUES (outbid_value, NOW(), 'Refund for being outbid on auction (' || NEW.auction || ')', outbid_user);
        
        -- Notify the outbid user
        INSERT INTO notification (message, type, creation_date, "user")
        VALUES (
            'You have been outbid on the auction: "' || auction_title || '". Your bid has been refunded.',
            'AUCTION OUTBID',
            NOW(),
            outbid_user
        );
    END IF;
   
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_and_refund_outbid
    AFTER INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE notify_and_refund_outbid_function();
    
--t18
CREATE OR REPLACE FUNCTION notify_winner_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    winner_id INTEGER;
    winner_username TEXT;
    follower RECORD;
BEGIN
    IF NEW.status = 'CLOSED' THEN
        -- Getting the ID of the auction winner
        SELECT INTO winner_id "user" FROM bid WHERE auction = NEW.id ORDER BY value DESC LIMIT 1;
        
        -- Getting the username of the winner
        SELECT INTO winner_username username FROM authenticated_user WHERE id = winner_id;
        
        -- Notifying the winner
        INSERT INTO notification (message, type, creation_date, "user")
        VALUES ('Congratulations, ' || winner_username || '! You won the auction: "' || NEW.title || '"', 'AUCTION WON', NOW(), winner_id);
        
        -- Notifying the followers
        FOR follower IN (SELECT "user" FROM following WHERE auction = NEW.id)
        LOOP
            INSERT INTO notification (message, type, creation_date, "user")
            VALUES (winner_username || ' won the auction: "' || NEW.title || '"', 'AUCTION END', NOW(), follower."user");
        END LOOP;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER notify_winner
    AFTER UPDATE OF status ON auction
    FOR EACH ROW
    WHEN (NEW.status = 'CLOSED')
    EXECUTE PROCEDURE notify_winner_function();                     
    
--t19
CREATE OR REPLACE FUNCTION notify_followers_auction_canceled_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.status = 'CANCELLED' THEN
        INSERT INTO notification (message, type, creation_date, "user")
        SELECT 
            'The auction "' || NEW.title || '" has been cancelled.',
            'AUCTION CANCELLED',
            NOW(),
            following."user"
        FROM following
        WHERE following.auction = NEW.id;
    END IF;
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER notify_followers_auction_canceled
    AFTER UPDATE OF status ON auction
    FOR EACH ROW
    WHEN (NEW.status = 'CANCELLED')
    EXECUTE PROCEDURE notify_followers_auction_canceled_function();                        
    
--t20
CREATE OR REPLACE FUNCTION prevent_admin_bid_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT 1 FROM authenticated_user WHERE id = NEW.user AND role = 'ADMIN') THEN
        RAISE EXCEPTION 'Admin users are not allowed to place bids.';
    END IF;
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER prevent_admin_bid
    BEFORE INSERT ON bid
    FOR EACH ROW
    EXECUTE PROCEDURE prevent_admin_bid_function(); 
    
--t21
CREATE OR REPLACE FUNCTION validate_rating_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.rating < 1 OR NEW.rating > 5 THEN
        RAISE EXCEPTION 'Rating value must be between 1 and 5.';
    END IF;
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER validate_rating
    BEFORE INSERT OR UPDATE OF rating ON review
    FOR EACH ROW
    EXECUTE PROCEDURE validate_rating_function(); 
    
--t22
CREATE OR REPLACE FUNCTION notify_admins_report_function() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO notification (message, type, creation_date, "user")
    SELECT 
        'A new report has been submitted for auction: ' || (SELECT title FROM auction WHERE id = NEW.auction),
        'REPORT',
        NOW(),
        authenticated_user.id
    FROM authenticated_user
    WHERE role = 'ADMIN';
    
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER notify_admins_report
    AFTER INSERT ON report_auction
    FOR EACH ROW
    EXECUTE PROCEDURE notify_admins_report_function();

    
--t23
CREATE OR REPLACE FUNCTION notify_followers_price_increase_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    follower RECORD;
BEGIN
    FOR follower IN (SELECT "user" FROM following WHERE auction = NEW.id)
    LOOP
        INSERT INTO notification (message, type, creation_date, "user", active)
        VALUES ('The current price for the auction "' || NEW.title || '" has been increased to ' || NEW.current_price || ' euros.', 
                'AUCTION BID', NOW(), follower."user", TRUE);
    END LOOP;
    
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER notify_followers_price_increase
    AFTER UPDATE OF current_price ON auction
    FOR EACH ROW
    WHEN (OLD.current_price IS DISTINCT FROM NEW.current_price)
    EXECUTE PROCEDURE notify_followers_price_increase_function();
    
--t24
CREATE OR REPLACE FUNCTION notify_owner_after_review_function() RETURNS TRIGGER AS
$BODY$
DECLARE
    auction_owner INTEGER;
    reviewer_username TEXT;
BEGIN
    -- Get the owner of the auction
    SELECT INTO auction_owner owner FROM auction WHERE id = NEW.auction;
    
    -- Get the username of the reviewer
    SELECT INTO reviewer_username username FROM authenticated_user WHERE id = NEW.reviewer;
    
    -- Insert a notification for the auction owner
    INSERT INTO notification (message, type, creation_date, "user", active)
    VALUES ('User ' || reviewer_username || ' has given ' || NEW.rating || ' stars in a review for your auction "' 
            || (SELECT title FROM auction WHERE id = NEW.auction) || '".', 
            'REVIEW', NOW(), auction_owner, TRUE);
    
    RETURN NEW;
END;
$BODY$
language plpgsql;

CREATE TRIGGER notify_owner_after_review
    AFTER INSERT ON review
    FOR EACH ROW
    EXECUTE PROCEDURE notify_owner_after_review_function();
    
--t25
CREATE OR REPLACE FUNCTION prevent_admin_create_auction() RETURNS TRIGGER AS
$BODY$
DECLARE 
    user_role ROLES;
BEGIN
    -- Get the role of the user trying to create the auction
    SELECT role INTO user_role 
    FROM authenticated_user
    WHERE id = NEW.owner;
    
    -- Check if the user role is ADMIN
    IF user_role = 'ADMIN' THEN
        RAISE EXCEPTION 'Admins are not allowed to create auctions.';
    END IF;
    
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER prevent_admin_create_auction_trigger
    BEFORE INSERT ON auction
    FOR EACH ROW
    EXECUTE FUNCTION prevent_admin_create_auction();

-- users
INSERT INTO authenticated_user (id, username, first_name, last_name, email, password, role, balance) VALUES
(1, 'sarah_m', 'Sarah', 'Mitchell', 'sarahm@example.com', 'password', 'ADMIN', 50),
(2, 'michael_j', 'Michael', 'Johnson', 'michaelj@example.com', 'password', 'USER', 50),
(3, 'linda_w', 'Linda', 'Williams', 'lindaw@example.com', 'password', 'USER', 50),
(4, 'james_b', 'James', 'Brown', 'jamesb@example.com', 'password', 'USER', 50),
(5, 'patricia_k', 'Patricia', 'King', 'patriciak@example.com', 'password', 'ADMIN', 50),
(6, 'david_l', 'David', 'Lee', 'davidl@example.com', 'password', 'USER', 50),
(7, 'susan_g', 'Susan', 'Garcia', 'susang@example.com', 'password', 'USER', 50),
(8, 'robert_a', 'Robert', 'Adams', 'roberta@example.com', 'password', 'USER', 50),
(9, 'karen_t', 'Karen', 'Taylor', 'karent@example.com', 'password', 'USER', 50),
(10, 'brian_h', 'Brian', 'Hernandez', 'brianh@example.com', 'password', 'USER', 50);

-- addresses
INSERT INTO address (id, street, postal_code, city, country, "user") VALUES
(1, '123 Main St', '10001', 'New York', 'USA', 1),
(2, '456 Elm St', '20002', 'Washington', 'USA', 2),
(3, '789 Oak St', '30003', 'Atlanta', 'USA', 3),
(4, '101 Maple St', '40004', 'Chicago', 'USA', 4),
(5, '102 Pine St', '50005', 'Denver', 'USA', 5),
(6, '103 Birch St', '60006', 'Los Angeles', 'USA', 6),
(7, '104 Cedar St', '70007', 'San Francisco', 'USA', 7),
(8, '105 Redwood St', '80008', 'Seattle', 'USA', 8),
(9, '106 Spruce St', '90009', 'Miami', 'USA', 9),
(10, '107 Aspen St', '10010', 'Boston', 'USA', 10);

-- items
INSERT INTO item (id, name, category, brand, color, picture, condition) VALUES
(1, 'Floral Tee', 'T-Shirt', 'Brand1', 'White', 'floral_tee.jpg', 'NEW'),
(2, 'Cotton Socks', 'Socks', 'Brand2', 'Black', 'cotton_socks.jpg', 'NEW'),
(3, 'Denim Jacket', 'Jacket', 'Brand3', 'Blue', 'denim_jacket.jpg', 'LIKE NEW'),
(4, 'Silk Scarf', 'Scarf', 'Brand4', 'Red', 'silk_scarf.jpg', 'NEW'),
(5, 'Leather Belt', 'Belt', 'Brand5', 'Brown', 'leather_belt.jpg', 'GOOD'),
(6, 'Running Shoes', 'Shoes', 'Brand1', 'Grey', 'running_shoes.jpg', 'EXCELLENT'),
(7, 'Winter Gloves', 'Gloves', 'Brand2', 'Black', 'winter_gloves.jpg', 'NEW'),
(8, 'Wool Hat', 'Hat', 'Brand3', 'Beige', 'wool_hat.jpg', 'LIKE NEW'),
(9, 'Sunglasses', 'Sunglasses', 'Brand4', 'Black', 'sunglasses.jpg', 'NEW'),
(10, 'Watch', 'Watch', 'Brand5', 'Silver', 'watch.jpg', 'GOOD');


-- auctions
INSERT INTO auction (id, title, description, start_date, end_date, starting_price, current_price, status, "owner", "item") VALUES
(1, 'Auction1', 'Nice shirt', '2023-01-01 00:00:00', '2023-01-10 00:00:00', 10, 10, 'ACTIVE', 3, 1),
(2, 'Auction2', 'Stylish pants', '2023-01-02 00:00:00', '2023-01-11 00:00:00', 20, 20, 'ACTIVE', 2, 2);

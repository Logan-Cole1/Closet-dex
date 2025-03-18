/* Stored Procedures and Triggers */

# CREATE PROCEDURES


#1 Procedure clo_create_user(): Create add new user to table
 
delimiter //

drop procedure if exists clo_create_user//

CREATE PROCEDURE clo_create_user (
	username varchar(30),
    password varchar(256)
)
BEGIN
    insert into clo_user (username, password) values (username, SHA2(password, 256));
END//


#2 Procedure clo_insert_clothing(): create a new clothing

drop procedure if exists clo_insert_clothing//

CREATE procedure clo_insert_clothing (
	username varchar(30),
	cName varchar(50),
	category enum("Headwear", "Top", "Outerwear", "Bottom", "Dress", "Footwear", "Accessories")
)
begin
    INSERT INTO clo_clothing_items (username, cName, category) VALUES (username, cName, category);
END//


#3 Procedure clo_create_outfit(): create a new outfit

drop procedure if exists clo_create_outfit//

CREATE PROCEDURE clo_create_outfit (
    username varchar(30),
    oName varchar(50),
    oImage varchar(500)
)
BEGIN
    insert into clo_outfits values (username, oName, oImage);
END//


#4 Procedure clo_insert_outfit_item(): add an article of clothing to an outfit

drop procedure if exists clo_insert_outfit_item//

CREATE PROCEDURE clo_insert_outfit_item (
    username varchar(30),
    oName varchar(100),
    cName varchar(50)
)
BEGIN
   insert into clo_outfit_items values (username, oName, cName);
END//


delimiter ;

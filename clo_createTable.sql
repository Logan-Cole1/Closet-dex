/* Create tables for Closidex*/

drop table if exists clo_outfit_items;
drop table if exists clo_outfits;
drop table if exists clo_clothing_items;
drop table if exists clo_user;

create table clo_user(
    username varchar(30) primary key,
    password varchar(256) NOT NULL
);

create table clo_clothing_items(
	username varchar(30),
    cName varchar(50),
    category enum("Headwear", "Top", "Outerwear", "Bottom", "Dress", "Footwear", "Accessories") NOT NULL,
    cImage varchar(500),
    foreign key (username) references clo_user(username),
    primary key (username, cName)
);

create table clo_outfits(
	username varchar(30),
    oName varchar(100),
    oImage varchar(500),
    foreign key (username) references clo_user(username),
    primary key (username, oName)
);

create table clo_outfit_items(
	username varchar(30),
    oName varchar(100),
    cName varchar(50),
    foreign key (username, cName) references clo_clothing_items(username, cName),
    foreign key (username, oName) references clo_outfits(username, oName),
    primary key (username, oName, cName)
);

describe clo_user;
describe clo_clothing_items;
describe clo_outfits;
describe clo_outfit_items;
